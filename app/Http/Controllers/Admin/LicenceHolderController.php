<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LicenceHolder;
use App\Support\CardSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LicenceHolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_licence_holders')->only(['index', 'show', 'card', 'download', 'print', 'printSheet', 'downloadSheet']);
        $this->middleware('permission:create_licence_holder')->only(['create', 'store']);
        $this->middleware('permission:edit_licence_holder')->only(['edit', 'update', 'gradeUpdates', 'applyGradeUpdates']);
        $this->middleware('permission:delete_licence_holder')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request);

        $licenceHolders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.licence-holders.index', [
            'licenceHolders' => $licenceHolders,
            'sections' => LicenceHolder::query()
                ->whereNotNull('club')
                ->distinct()
                ->orderBy('club')
                ->pluck('club'),
            'salles' => LicenceHolder::query()
                ->whereNotNull('salle')
                ->distinct()
                ->orderBy('salle')
                ->pluck('salle'),
            'grades' => $this->grades(),
            'page_title' => __('messages.cards.title'),
            'active_menu' => 'cards',
        ]);
    }

    public function create(): View
    {
        return view('admin.licence-holders.create', [
            'licenceHolder' => new LicenceHolder([
                'licence_number' => LicenceHolder::generateLicenceNumber(),
                'status' => 'active',
                'issued_at' => now(),
            ]),
            'statuses' => $this->statuses(),
            'grades' => $this->grades(),
            'page_title' => __('messages.cards.add'),
            'active_menu' => 'cards',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        unset($data['photo'], $data['birth_certificate'], $data['remove_photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('licence-holders/photos', 'public');
        }

        if ($request->hasFile('birth_certificate')) {
            $data['birth_certificate_path'] = $request->file('birth_certificate')->store('licence-holders/birth-certificates', 'public');
        }

        $licenceHolder = LicenceHolder::create($data);

        return redirect()
            ->route('admin.cards.show', $licenceHolder)
            ->with('success', __('messages.cards.created', ['name' => $licenceHolder->full_name]));
    }

    public function show(LicenceHolder $licenceHolder): View
    {
        $licenceHolder->load('creator');

        return view('admin.licence-holders.show', [
            'licenceHolder' => $licenceHolder,
            'statuses' => $this->statuses(),
            'settings' => CardSettings::all(),
            'qrCode' => $this->qrCodeDataUri($licenceHolder),
            'page_title' => $licenceHolder->full_name,
            'active_menu' => 'cards',
        ]);
    }

    public function edit(LicenceHolder $licenceHolder): View
    {
        return view('admin.licence-holders.edit', [
            'licenceHolder' => $licenceHolder,
            'statuses' => $this->statuses(),
            'grades' => $this->grades(),
            'page_title' => __('messages.users.edit_named', ['name' => $licenceHolder->full_name]),
            'active_menu' => 'cards',
        ]);
    }

    public function update(Request $request, LicenceHolder $licenceHolder): RedirectResponse
    {
        $data = $this->validatedData($request, $licenceHolder);
        unset($data['photo'], $data['birth_certificate'], $data['remove_photo']);

        if ($request->boolean('remove_photo')) {
            $this->deletePhoto($licenceHolder);
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            $this->deletePhoto($licenceHolder);
            $data['photo_path'] = $request->file('photo')->store('licence-holders/photos', 'public');
        }

        if ($request->hasFile('birth_certificate')) {
            $this->deleteBirthCertificate($licenceHolder);
            $data['birth_certificate_path'] = $request->file('birth_certificate')->store('licence-holders/birth-certificates', 'public');
        }

        $licenceHolder->update($data);

        return redirect()
            ->route('admin.cards.show', $licenceHolder)
            ->with('success', __('messages.cards.updated', ['name' => $licenceHolder->full_name]));
    }

    public function card(LicenceHolder $licenceHolder): View
    {
        return view('admin.licence-holders.card', [
            'licenceHolder' => $licenceHolder,
            'settings' => CardSettings::all(),
            'qrCode' => $this->qrCodeDataUri($licenceHolder),
            'page_title' => __('messages.cards.title') . ' : ' . $licenceHolder->full_name,
            'active_menu' => 'cards',
        ]);
    }

    public function print(LicenceHolder $licenceHolder): View
    {
        $licenceHolders = collect([$licenceHolder]);

        return view('admin.licence-holders.sheet', [
            'licenceHolders' => $licenceHolders,
            'settings' => CardSettings::all(),
            'qrCodes' => $this->qrCodesFor($licenceHolders),
            'page_title' => __('messages.cards.title') . ' : ' . $licenceHolder->full_name,
            'active_menu' => 'cards',
        ]);
    }

    public function download(LicenceHolder $licenceHolder)
    {
        $licenceHolders = collect([$licenceHolder]);

        $pdf = Pdf::loadView('admin.licence-holders.sheet-pdf', [
            'licenceHolders' => $licenceHolders,
            'settings' => CardSettings::all(),
            'qrCodes' => $this->qrCodesFor($licenceHolders),
            'pdfMode' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('carte-' . $licenceHolder->licence_number . '.pdf');
    }

    public function printSheet(Request $request): View
    {
        $licenceHolders = $this->filteredQuery($request)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.licence-holders.sheet', [
            'licenceHolders' => $licenceHolders,
            'settings' => CardSettings::all(),
            'qrCodes' => $this->qrCodesFor($licenceHolders),
            'page_title' => __('messages.cards.sheet_title'),
            'active_menu' => 'cards',
        ]);
    }

    public function downloadSheet(Request $request)
    {
        $licenceHolders = $this->filteredQuery($request)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $pdf = Pdf::loadView('admin.licence-holders.sheet-pdf', [
            'licenceHolders' => $licenceHolders,
            'settings' => CardSettings::all(),
            'qrCodes' => $this->qrCodesFor($licenceHolders),
            'pdfMode' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('planche-cartes.pdf');
    }

    public function gradeUpdates(Request $request): View
    {
        $licenceHolders = $this->filteredQuery($request)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.licence-holders.grade-updates', [
            'licenceHolders' => $licenceHolders,
            'sections' => LicenceHolder::query()
                ->whereNotNull('club')
                ->distinct()
                ->orderBy('club')
                ->pluck('club'),
            'salles' => LicenceHolder::query()
                ->whereNotNull('salle')
                ->distinct()
                ->orderBy('salle')
                ->pluck('salle'),
            'grades' => $this->grades(),
            'nextGrades' => $this->nextGradeMap(),
            'page_title' => __('messages.cards.grades_update_title'),
            'active_menu' => 'cards',
        ]);
    }

    public function applyGradeUpdates(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'holder_ids' => ['required', 'array', 'min:1'],
            'holder_ids.*' => ['integer', 'exists:licence_holders,id'],
            'update_mode' => ['required', Rule::in(['next', 'custom'])],
            'target_grade' => ['nullable', Rule::in($this->grades())],
            'refresh_issued_at' => ['nullable', 'boolean'],
        ], [
            'holder_ids.required' => 'Veuillez sélectionner au moins une carte à mettre à jour.',
            'target_grade.in' => 'Le grade choisi est invalide.',
        ]);

        if ($validated['update_mode'] === 'custom' && blank($validated['target_grade'] ?? null)) {
            return back()
                ->withInput()
                ->with('warning', 'Veuillez choisir le grade à appliquer.');
        }

        $holders = LicenceHolder::whereIn('id', $validated['holder_ids'])->get();
        $nextGrades = $this->nextGradeMap();
        $updated = 0;
        $skipped = 0;

        foreach ($holders as $holder) {
            $targetGrade = $validated['update_mode'] === 'next'
                ? ($nextGrades[$holder->grade] ?? null)
                : $validated['target_grade'];

            if (!$targetGrade || $targetGrade === 'Aucun grade supérieur') {
                $skipped++;
                continue;
            }

            $payload = ['grade' => $targetGrade];

            if ($request->boolean('refresh_issued_at')) {
                $payload['issued_at'] = now();
            }

            $holder->update($payload);
            $updated++;
        }

        $message = "{$updated} carte(s) mise(s) à jour.";

        if ($skipped > 0) {
            $message .= " {$skipped} carte(s) ignorée(s), faute de grade suivant.";
        }

        return redirect()
            ->route('admin.cards.grade-updates', $request->only(['search', 'salle', 'section', 'grade']))
            ->with('success', $message);
    }

    public function destroy(LicenceHolder $licenceHolder): RedirectResponse
    {
        $name = $licenceHolder->full_name;

        $this->deletePhoto($licenceHolder);
        $this->deleteBirthCertificate($licenceHolder);
        $licenceHolder->forceDelete();

        return redirect()
            ->route('admin.cards.index')
            ->with('success', "La carte de {$name} a été supprimée avec succès.");
    }

    private function validatedData(Request $request, ?LicenceHolder $licenceHolder = null): array
    {
        $data = $request->validate([
            'licence_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('licence_holders', 'licence_number')->ignore($licenceHolder),
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['M', 'F'])],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'grade' => ['nullable', Rule::in($this->gradeValidationValues())],
            'club' => ['nullable', 'string', 'max:150'],
            'salle' => ['nullable', 'string', 'max:150'],
            'domicile' => ['nullable', 'string', 'max:150'],
            'birth_act_number' => ['nullable', 'string', 'max:100'],
            'nina' => ['nullable', 'string', 'max:100'],
            'birth_act_region' => ['nullable', 'string', 'max:150'],
            'birth_act_cercle' => ['nullable', 'string', 'max:150'],
            'birth_act_arrondissement' => ['nullable', 'string', 'max:150'],
            'birth_act_commune' => ['nullable', 'string', 'max:150'],
            'birth_act_center' => ['nullable', 'string', 'max:150'],
            'father_first_name' => ['nullable', 'string', 'max:150'],
            'father_last_name' => ['nullable', 'string', 'max:150'],
            'father_profession' => ['nullable', 'string', 'max:150'],
            'father_domicile' => ['nullable', 'string', 'max:150'],
            'mother_first_name' => ['nullable', 'string', 'max:150'],
            'mother_last_name' => ['nullable', 'string', 'max:150'],
            'mother_profession' => ['nullable', 'string', 'max:150'],
            'mother_domicile' => ['nullable', 'string', 'max:150'],
            'civil_officer_name' => ['nullable', 'string', 'max:150'],
            'civil_officer_quality' => ['nullable', 'string', 'max:150'],
            'birth_act_established_at' => ['nullable', 'date'],
            'birth_act_certified_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
            'issued_at' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'birth_certificate' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['grade'])) {
            $data['grade'] = str_replace('Keup', 'Kieup', $data['grade']);
        }

        return $data;
    }

    private function filteredQuery(Request $request)
    {
        $query = LicenceHolder::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $terms = collect(preg_split('/\s+/', $search))->filter()->values();

            $query->where(function ($q) use ($search, $terms) {
                $q->where('licence_number', 'like', "%{$search}%")
                    ->orWhere(function ($nameQuery) use ($terms) {
                        foreach ($terms as $term) {
                            $nameQuery->where(function ($termQuery) use ($term) {
                                $termQuery->where('first_name', 'like', "%{$term}%")
                                    ->orWhere('last_name', 'like', "%{$term}%");
                            });
                        }
                    });
            });
        }

        if ($request->filled('salle')) {
            $query->where('salle', $request->salle);
        }

        if ($request->filled('section')) {
            $query->where('club', $request->section);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        return $query;
    }

    private function qrCodesFor($licenceHolders): array
    {
        return $licenceHolders
            ->mapWithKeys(fn (LicenceHolder $holder) => [$holder->id => $this->qrCodeDataUri($holder)])
            ->all();
    }

    private function deletePhoto(LicenceHolder $licenceHolder): void
    {
        if ($licenceHolder->photo_path && Storage::disk('public')->exists($licenceHolder->photo_path)) {
            Storage::disk('public')->delete($licenceHolder->photo_path);
        }
    }

    private function deleteBirthCertificate(LicenceHolder $licenceHolder): void
    {
        if ($licenceHolder->birth_certificate_path && Storage::disk('public')->exists($licenceHolder->birth_certificate_path)) {
            Storage::disk('public')->delete($licenceHolder->birth_certificate_path);
        }
    }

    private function qrCodeDataUri(LicenceHolder $licenceHolder): string
    {
        $result = (new Builder(
            writer: new PngWriter(),
            data: $this->qrCodePayload($licenceHolder),
            size: 220,
            margin: 0
        ))->build();

        return $result->getDataUri();
    }

    private function qrCodePayload(LicenceHolder $licenceHolder): string
    {
        $lines = [
            'LICENCE REGIONALE TAEKWONDO',
            'Nom: ' . $licenceHolder->full_name,
            'N licence: ' . ($licenceHolder->licence_number ?? '-'),
            'Grade: ' . ($licenceHolder->grade ?? '-'),
            'Sexe: ' . ($licenceHolder->gender === 'F' ? 'Feminin' : 'Masculin'),
            'Naissance: ' . ($licenceHolder->birth_date?->format('d/m/Y') ?? '-'),
            'Lieu: ' . ($licenceHolder->birth_place ?? '-'),
            'Section: ' . ($licenceHolder->club ?? '-'),
            'Salle: ' . ($licenceHolder->salle ?? '-'),
            'Telephone: ' . ($licenceHolder->phone ?? '-'),
            'Domicile: ' . ($licenceHolder->domicile ?? '-'),
            'Statut: ' . $licenceHolder->status_label,
            'Delivree le: ' . ($licenceHolder->issued_at?->format('d/m/Y') ?? '-'),
        ];

        return implode("\n", $lines);
    }

    private function statuses(): array
    {
        return [
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
        ];
    }

    private function grades(): array
    {
        $keups = collect(range(9, 1))->map(fn (int $grade) => $grade === 1 ? '1er Kieup' : "{$grade}e Kieup");
        $dans = collect(range(1, 9))->map(fn (int $grade) => $grade === 1 ? '1er Dan' : "{$grade}e Dan");

        return $keups->merge($dans)->all();
    }

    private function nextGradeMap(): array
    {
        $grades = $this->grades();

        return collect($grades)
            ->mapWithKeys(fn (string $grade, int $index) => [$grade => $grades[$index + 1] ?? 'Aucun grade supérieur'])
            ->all();
    }

    private function gradeValidationValues(): array
    {
        $legacyKeups = collect(range(9, 1))->map(fn (int $grade) => $grade === 1 ? '1er Keup' : "{$grade}e Keup");

        return collect($this->grades())->merge($legacyKeups)->all();
    }
}
