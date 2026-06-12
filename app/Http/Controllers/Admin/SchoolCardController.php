<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolCard;
use App\Support\SchoolCardSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_school_cards')->only(['index', 'show', 'card', 'print', 'download']);
        $this->middleware('permission:create_school_card')->only(['create', 'store']);
        $this->middleware('permission:edit_school_card')->only(['edit', 'update']);
        $this->middleware('permission:delete_school_card')->only(['destroy']);
        $this->middleware('permission:manage_school_card_settings')->only(['settings', 'updateSettings']);
    }

    public function index(Request $request): View
    {
        $schoolCards = $this->filteredQuery($request)->latest()->paginate(15)->withQueryString();
        $optionsQuery = $this->ownedQuery();

        return view('admin.school-cards.index', [
            'schoolCards' => $schoolCards,
            'classes' => (clone $optionsQuery)->whereNotNull('class_name')->distinct()->orderBy('class_name')->pluck('class_name'),
            'schools' => (clone $optionsQuery)->whereNotNull('school_name')->distinct()->orderBy('school_name')->pluck('school_name'),
            'page_title' => __('messages.school_cards.title'),
            'active_menu' => 'school_cards',
        ]);
    }

    public function create(): View
    {
        $settings = SchoolCardSettings::all(auth()->id());

        return view('admin.school-cards.create', [
            'schoolCard' => new SchoolCard([
                'card_number' => SchoolCard::generateCardNumber(),
                'academy' => $settings['official']['academy'],
                'cap' => $settings['official']['cap'],
                'school_name' => $settings['official']['school_name'],
                'school_type' => $settings['official']['school_type'],
                'academic_year' => $settings['official']['academic_year'],
                'status' => 'active',
                'issued_at' => now(),
            ]),
            'page_title' => __('messages.school_cards.add'),
            'active_menu' => 'school_cards',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        unset($data['photo'], $data['birth_certificate'], $data['remove_photo']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('school-cards/photos', 'public');
        }

        if ($request->hasFile('birth_certificate')) {
            $data['birth_certificate_path'] = $request->file('birth_certificate')->store('school-cards/birth-certificates', 'public');
        }

        $schoolCard = SchoolCard::create($data);

        return redirect()
            ->route('admin.school-cards.show', $schoolCard)
            ->with('success', __('messages.school_cards.created', ['name' => $schoolCard->full_name]));
    }

    public function show(SchoolCard $schoolCard): View
    {
        $this->authorizeSchoolCard($schoolCard);

        return view('admin.school-cards.show', [
            'schoolCard' => $schoolCard,
            'settings' => $this->settingsForCard($schoolCard),
            'qrCode' => $this->qrCodeDataUri($schoolCard),
            'page_title' => $schoolCard->full_name,
            'active_menu' => 'school_cards',
        ]);
    }

    public function edit(SchoolCard $schoolCard): View
    {
        $this->authorizeSchoolCard($schoolCard);

        return view('admin.school-cards.edit', [
            'schoolCard' => $schoolCard,
            'page_title' => __('messages.school_cards.edit_named', ['name' => $schoolCard->full_name]),
            'active_menu' => 'school_cards',
        ]);
    }

    public function update(Request $request, SchoolCard $schoolCard): RedirectResponse
    {
        $this->authorizeSchoolCard($schoolCard);

        $data = $this->validatedData($request, $schoolCard);
        unset($data['photo'], $data['birth_certificate'], $data['remove_photo']);

        if ($request->boolean('remove_photo')) {
            $this->deletePhoto($schoolCard);
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            $this->deletePhoto($schoolCard);
            $data['photo_path'] = $request->file('photo')->store('school-cards/photos', 'public');
        }

        if ($request->hasFile('birth_certificate')) {
            $this->deleteBirthCertificate($schoolCard);
            $data['birth_certificate_path'] = $request->file('birth_certificate')->store('school-cards/birth-certificates', 'public');
        }

        $schoolCard->update($data);

        return redirect()
            ->route('admin.school-cards.show', $schoolCard)
            ->with('success', __('messages.school_cards.updated', ['name' => $schoolCard->full_name]));
    }

    public function destroy(SchoolCard $schoolCard): RedirectResponse
    {
        $this->authorizeSchoolCard($schoolCard);

        $name = $schoolCard->full_name;
        $this->deletePhoto($schoolCard);
        $this->deleteBirthCertificate($schoolCard);
        $schoolCard->forceDelete();

        return redirect()
            ->route('admin.school-cards.index')
            ->with('success', __('messages.school_cards.deleted', ['name' => $name]));
    }

    public function card(SchoolCard $schoolCard): View
    {
        $this->authorizeSchoolCard($schoolCard);

        return view('admin.school-cards.card', [
            'schoolCard' => $schoolCard,
            'settings' => $this->settingsForCard($schoolCard),
            'qrCode' => $this->qrCodeDataUri($schoolCard),
            'page_title' => __('messages.school_cards.card_for', ['name' => $schoolCard->full_name]),
            'active_menu' => 'school_cards',
        ]);
    }

    public function print(SchoolCard $schoolCard): View
    {
        $this->authorizeSchoolCard($schoolCard);

        return view('admin.school-cards.print', [
            'schoolCard' => $schoolCard,
            'settings' => $this->settingsForCard($schoolCard),
            'qrCode' => $this->qrCodeDataUri($schoolCard),
        ]);
    }

    public function download(SchoolCard $schoolCard)
    {
        $this->authorizeSchoolCard($schoolCard);

        $pdf = Pdf::loadView('admin.school-cards.pdf', [
            'schoolCard' => $schoolCard,
            'settings' => $this->settingsForCard($schoolCard),
            'qrCode' => $this->qrCodeDataUri($schoolCard),
            'pdfMode' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('carte-scolaire-' . $schoolCard->card_number . '.pdf');
    }

    public function settings(): View
    {
        return view('admin.school-cards.settings', [
            'settings' => SchoolCardSettings::all(auth()->id()),
            'page_title' => __('messages.school_cards.settings'),
            'active_menu' => 'school_cards',
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ministry' => ['nullable', 'string', 'max:255'],
            'academy' => ['nullable', 'string', 'max:255'],
            'cap' => ['nullable', 'string', 'max:255'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'school_type' => ['nullable', 'string', 'max:80'],
            'academic_year' => ['nullable', 'string', 'max:30'],
            'header_fields' => ['nullable', 'array'],
            'header_fields.*' => ['string', Rule::in(['ministry', 'academy', 'cap', 'institution', 'class_name'])],
            'student_fields' => ['nullable', 'array'],
            'student_fields.*' => ['string', Rule::in(['full_name', 'matricule', 'gender', 'birth_date', 'birth_place', 'academic_year', 'card_number'])],
            'custom_lines' => ['nullable', 'array'],
            'custom_lines.*.label' => ['nullable', 'string', 'max:80'],
            'custom_lines.*.value' => ['nullable', 'string', 'max:150'],
            'primary_color' => ['required', 'string', 'max:20'],
            'secondary_color' => ['required', 'string', 'max:20'],
            'background_color' => ['required', 'string', 'max:20'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'signature_data' => ['nullable', 'string'],
            'background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'decorative_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_signature' => ['nullable', 'boolean'],
            'remove_background_image' => ['nullable', 'boolean'],
            'remove_decorative_image' => ['nullable', 'boolean'],
        ]);

        $settings = SchoolCardSettings::all(auth()->id());

        $payload = [
            'official' => [
                'ministry' => $validated['ministry'] ?? '',
                'academy' => $validated['academy'] ?? '',
                'cap' => $validated['cap'] ?? '',
                'school_name' => $validated['school_name'] ?? '',
                'school_type' => Str::upper(trim((string) ($validated['school_type'] ?? ''))),
                'academic_year' => $validated['academic_year'] ?? '',
            ],
            'card' => [
                'primary_color' => $validated['primary_color'],
                'secondary_color' => $validated['secondary_color'],
                'background_color' => $validated['background_color'],
            ],
            'display' => [
                'header_fields' => array_values($validated['header_fields'] ?? []),
                'student_fields' => array_values($validated['student_fields'] ?? []),
                'custom_lines' => collect($validated['custom_lines'] ?? [])
                    ->filter(fn ($line) => filled($line['label'] ?? null) && filled($line['value'] ?? null))
                    ->map(fn ($line) => [
                        'label' => trim($line['label']),
                        'value' => trim($line['value']),
                    ])
                    ->values()
                    ->all(),
            ],
        ];

        $this->handleSettingsFile($request, $settings, $payload, 'signature', 'signature_path', 'school-card-settings/signatures');
        $this->handleSettingsFile($request, $settings['card'], $payload['card'], 'background_image', 'background_image_path', 'school-card-settings/backgrounds');
        $this->handleSettingsFile($request, $settings['card'], $payload['card'], 'decorative_image', 'decorative_image_path', 'school-card-settings/decorations');

        if ($request->filled('signature_data')) {
            if (!empty($settings['signature_path']) && Storage::disk('public')->exists($settings['signature_path'])) {
                Storage::disk('public')->delete($settings['signature_path']);
            }
            $payload['signature_path'] = $this->storeSignatureData($request->string('signature_data')->toString());
        }

        SchoolCardSettings::save($payload, auth()->id());

        return back()->with('success', __('messages.school_cards.settings_updated'));
    }

    private function validatedData(Request $request, ?SchoolCard $schoolCard = null): array
    {
        $validated = $request->validate([
            'card_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('school_cards', 'card_number')->ignore($schoolCard),
            ],
            'matricule' => ['nullable', 'string', 'max:80'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['M', 'F'])],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:150'],
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
            'academy' => ['nullable', 'string', 'max:150'],
            'cap' => ['nullable', 'string', 'max:150'],
            'school_name' => ['nullable', 'string', 'max:150'],
            'school_type' => ['nullable', 'string', 'max:80'],
            'class_name' => ['required', 'string', 'max:80'],
            'academic_year' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
            'issued_at' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'birth_certificate' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('school_type', $validated)) {
            $validated['school_type'] = Str::upper(trim((string) $validated['school_type']));
        }

        return $validated;
    }

    private function filteredQuery(Request $request)
    {
        $query = $this->ownedQuery();

        if ($search = $request->string('search')->trim()->toString()) {
            $terms = collect(preg_split('/\s+/', $search))->filter()->values();

            $query->where(function ($q) use ($search, $terms) {
                $q->where('card_number', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%")
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

        if ($request->filled('school_name')) {
            $query->where('school_name', $request->school_name);
        }

        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }

        return $query;
    }

    private function qrCodeDataUri(SchoolCard $schoolCard): string
    {
        $result = (new Builder(
            writer: new PngWriter(),
            data: $this->qrCodePayload($schoolCard),
            size: 220,
            margin: 0
        ))->build();

        return $result->getDataUri();
    }

    private function qrCodePayload(SchoolCard $schoolCard): string
    {
        return implode("\n", [
            'CARTE IDENTITE SCOLAIRE',
            'Nom: ' . $schoolCard->full_name,
            'Matricule: ' . ($schoolCard->matricule ?? '-'),
            'Carte: ' . ($schoolCard->card_number ?? '-'),
            'Classe: ' . ($schoolCard->class_name ?? '-'),
            'Etablissement: ' . ($schoolCard->school_name ?? '-'),
            'Annee scolaire: ' . ($schoolCard->academic_year ?? '-'),
        ]);
    }

    private function deletePhoto(SchoolCard $schoolCard): void
    {
        if ($schoolCard->photo_path && Storage::disk('public')->exists($schoolCard->photo_path)) {
            Storage::disk('public')->delete($schoolCard->photo_path);
        }
    }

    private function deleteBirthCertificate(SchoolCard $schoolCard): void
    {
        if ($schoolCard->birth_certificate_path && Storage::disk('public')->exists($schoolCard->birth_certificate_path)) {
            Storage::disk('public')->delete($schoolCard->birth_certificate_path);
        }
    }

    private function ownedQuery()
    {
        $query = SchoolCard::query();

        if (!auth()->user()?->isSuperAdmin()) {
            $query->where('created_by', auth()->id());
        }

        return $query;
    }

    private function authorizeSchoolCard(SchoolCard $schoolCard): void
    {
        if (!auth()->user()?->isSuperAdmin() && (int) $schoolCard->created_by !== (int) auth()->id()) {
            abort(403, 'Accès refusé.');
        }
    }

    private function settingsForCard(SchoolCard $schoolCard): array
    {
        return SchoolCardSettings::all($schoolCard->created_by ?: auth()->id());
    }

    private function handleSettingsFile(Request $request, array $current, array &$payload, string $input, string $key, string $directory): void
    {
        if ($request->boolean('remove_' . $input)) {
            if (!empty($current[$key]) && Storage::disk('public')->exists($current[$key])) {
                Storage::disk('public')->delete($current[$key]);
            }
            $payload[$key] = null;
        }

        if ($request->hasFile($input)) {
            if (!empty($current[$key]) && Storage::disk('public')->exists($current[$key])) {
                Storage::disk('public')->delete($current[$key]);
            }
            $payload[$key] = SchoolCardSettings::storeFile($request->file($input), $directory);
        }
    }

    private function storeSignatureData(string $dataUri): string
    {
        if (!preg_match('/^data:image\/png;base64,/', $dataUri)) {
            abort(422, 'Signature invalide.');
        }

        $content = base64_decode(substr($dataUri, strpos($dataUri, ',') + 1), true);

        if ($content === false) {
            abort(422, 'Signature invalide.');
        }

        $path = 'school-card-settings/signatures/signature-' . Str::uuid() . '.png';
        Storage::disk('public')->put($path, $content);

        return $path;
    }

    private function statuses(): array
    {
        return [
            'active' => __('messages.statuses.active'),
            'inactive' => __('messages.statuses.inactive'),
            'suspended' => __('messages.statuses.suspended'),
        ];
    }
}
