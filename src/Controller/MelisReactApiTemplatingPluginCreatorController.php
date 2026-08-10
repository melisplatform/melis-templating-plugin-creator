<?php

namespace MelisTemplatingPluginCreator\Controller;

use Laminas\Form\Factory as FormFactory;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Session\Container;
use MelisCore\Controller\MelisAbstractActionController;
use MelisReactApi\Controller\CapabilityGuardTrait;

/**
 * API REST pour l'outil « Templating Plugin Creator ».
 *
 * Couche API du back-office React ; l'UI est livrée par la BRIQUE du module (public/ui-react),
 * donc l'outil n'apparaît que si MelisTemplatingPluginCreator est ACTIF. Contrat
 * `{ success, data, error }`. Calqué sur MelisReactApiCron / MelisReactApiNewsletter.
 *
 * ── Principe directeur ────────────────────────────────────────────────────────────────────────
 * L'outil legacy est un ASSISTANT en 6 étapes qui GÉNÈRE du code (fichiers PHP d'un plugin de
 * templating, réécriture de module.config.php / Module.php / fichiers de langue, activation du
 * module sur un site). On ne réimplémente RIEN de cette logique côté React :
 *
 *   • la VALIDATION réutilise les formulaires Laminas déclarés dans config/app.tools.php
 *     (`getFormMergedAndOrdered`) — mêmes validateurs, mêmes messages, mêmes règles métier
 *     (mot réservé PHP, module déjà existant, nom de plugin déjà pris, doublon de champ, digits) ;
 *   • la GÉNÉRATION appelle MelisTemplatingPluginCreatorService::generateTemplatingPlugin()
 *     (+ MelisToolCreatorService::createTool() pour la branche « nouveau module ») ;
 *   • l'ÉTAT de l'assistant est écrit dans le MÊME conteneur de session que le legacy
 *     (`templatingplugincreator` → `melis-templatingplugincreator`), car le service le lit dans
 *     son constructeur. React ne fait que présenter et poster des JSON propres.
 *
 * ⚠ MelisTemplatingPluginCreatorService prend un INSTANTANÉ de la session dans son constructeur
 *   et le ServiceManager met l'instance en cache : ne jamais le récupérer AVANT d'avoir fini
 *   d'écrire l'état de l'étape courante dans la même requête.
 *
 * ── Droits ────────────────────────────────────────────────────────────────────────────────────
 * Deux niveaux, tous deux appliqués côté serveur (le React ne fait que masquer les boutons) :
 *   1. accès-outil : `denyUnlessAccess()` → MelisCoreRights::canAccess('melistemplatingplugincreator_tool')
 *   2. capacités avancées : `denyUnlessCan()` sur l'onglet ET son action (cf. react.capabilities.php).
 *      Un admin qui décoche l'ONGLET « Finalisation » interdit `finalization` ; décocher seulement
 *      sa case « Création » interdit `finalization.create`. On vérifie les deux → décocher l'onglet
 *      suffit à fermer toutes ses actions.
 * Le contrôleur legacy, lui, n'avait AUCUN contrôle de droits : n'importe quel utilisateur
 * authentifié pouvait écrire des fichiers PHP dans le dépôt et activer un module.
 */
class MelisReactApiTemplatingPluginCreatorController extends MelisAbstractActionController
{
    use CapabilityGuardTrait;

    private const MELIS_KEY = 'melistemplatingplugincreator_tool';

    /** Conteneur de session partagé avec le contrôleur legacy (ne pas renommer). */
    private const SESSION_NS   = 'templatingplugincreator';
    private const SESSION_ROOT = 'melis-templatingplugincreator';

    private const NEW_MODULE = 'new_module';
    private const DROPDOWN   = 'Dropdown';
    private const TAB        = 1;   // multi-onglets de propriétés = « version 2 » côté legacy
    private const MAX_FIELDS = 25;  // borne du validateur Between de tpc_main_property_field_count

    /** Types d'affichage dont la valeur par défaut doit être numérique (validateur Digits). */
    private const NUMERIC_TYPES = ['NumericInput', 'PageInput'];

    // ════════════════════════════════════════════════════════════════════════
    //  CONTEXTE / ÉTAT
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Tout ce dont la brique a besoin pour se peindre : préflight d'environnement (le legacy
     * bloque l'outil si `module/` n'est pas inscriptible, si le dossier de vignettes ne l'est pas,
     * ou si GD manque), méta des 6 étapes, langues de la plateforme, modules de site, sites,
     * et les types d'affichage de champ (lus depuis le formulaire legacy → une seule source).
     */
    public function contextAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        try {
            $sm         = $this->getServiceManager();
            $translator = $sm->get('translator');
            $config     = $sm->get('MelisCoreConfig');

            // ── Préflight (mêmes contrôles que renderToolContentAction) ──
            $moduleDir = $_SERVER['DOCUMENT_ROOT'] . '/../module';
            $thumbCfg  = (array) $config->getItem('melistemplatingplugincreator/datas/plugin_thumbnail');
            $thumbDir  = (string) ($thumbCfg['path'] ?? '');

            $blocking = [];
            if (!is_writable($moduleDir)) {
                $blocking[] = $translator->translate('tr_melistemplatingplugincreator_fp_module');
            }
            if ($thumbDir !== '' && !is_dir($thumbDir)) {
                @mkdir($thumbDir, 0755, true);
            }
            if ($thumbDir === '' || !is_dir($thumbDir) || !is_writable($thumbDir)) {
                $blocking[] = $translator->translate('tr_melistemplatingplugincreator_fp_temp_thumbnail');
            }
            if (!extension_loaded('gd')) {
                $blocking[] = $translator->translate('tr_melistemplatingplugincreator_gd_library_not_found');
            }

            // ── Étapes (libellés traduits) ──
            $steps = [];
            foreach ((array) $config->getItem('melistemplatingplugincreator/datas/steps') as $key => $step) {
                $steps[] = [
                    'key'  => $key,
                    'name' => $translator->translate($step['name'] ?? $key),
                    'icon' => $step['icon'] ?? '',
                ];
            }

            // ── Types d'affichage : lus dans le formulaire legacy (source unique) ──
            $fieldForm    = $this->form('melistemplatingplugincreator_step3_field_form');
            $displayTypes = [];
            foreach ((array) $fieldForm->get('tpc_field_display_type')->getValueOptions() as $value => $label) {
                $displayTypes[] = ['value' => (string) $value, 'label' => (string) $label];
            }

            return $this->ok([
                'blocking'      => $blocking,               // non vide ⇒ la brique affiche l'erreur et rien d'autre
                'steps'         => $steps,
                'languages'     => $this->languages(),
                'currentLocale' => $this->currentLocale(),
                'siteModules'   => array_values((array) $sm->get('ModulesService')->getSitesModules()),
                'sites'         => $this->sites(),
                'displayTypes'  => $displayTypes,
                'maxFields'     => self::MAX_FIELDS,
                'thumbnail'     => [
                    'minSize' => (int) ($thumbCfg['min_size'] ?? 1),
                    'maxSize' => (int) ($thumbCfg['max_size'] ?? 512000),
                    'accept'  => '.gif,.jpg,.jpeg,.png',
                ],
            ]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /** État courant de l'assistant (conteneur de session) → la brique restaure l'UI après un reload. */
    public function stateAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        try {
            return $this->ok($this->publicState());
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /** Réinitialise l'assistant : purge la session ET la vignette temporaire sur disque. */
    public function resetAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('wizard')) { return $deny; }
        if ($deny = $this->denyUnlessCan('wizard.edit')) { return $deny; }
        try {
            $this->deleteThumbnailDir();
            $this->setState(['sessionID' => $this->sessionId()]);
            return $this->ok($this->publicState());
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    // ════════════════════════════════════════════════════════════════════════
    //  ÉTAPES 1 → 4 (validation + persistance)
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Valide et enregistre une étape de configuration. `POST /melis/react-api/tpc/step/:step`.
     * Réponse : `{ success:true, data:{ valid:bool, errors:{...} } }` — `valid:false` n'est PAS une
     * erreur HTTP, c'est un résultat de validation métier (la brique affiche les messages).
     */
    public function stepAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('wizard')) { return $deny; }
        if ($deny = $this->denyUnlessCan('wizard.edit')) { return $deny; }
        try {
            $step    = (int) $this->params()->fromRoute('step', 0);
            $payload = $this->jsonBody();

            $result = match ($step) {
                1 => $this->saveStep1($payload),
                2 => $this->saveStep2($payload),
                3 => $this->saveStep3($payload),
                4 => $this->saveStep4($payload),
                default => null,
            };
            if ($result === null) { return $this->bad('Unknown step'); }

            return $this->ok($result);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /**
     * Étape 1 — nom du plugin + destination (nouveau module / module de site existant).
     * Reprend les 3 règles métier du legacy : mot réservé PHP, module déjà présent sur la
     * plateforme, plugin de templating déjà déclaré dans le module de site choisi.
     */
    private function saveStep1(array $p): array
    {
        $data = [
            'tpc_plugin_name'          => trim((string) ($p['tpc_plugin_name'] ?? '')),
            'tpc_plugin_destination'   => (string) ($p['tpc_plugin_destination'] ?? ''),
            'tpc_new_module_name'      => trim((string) ($p['tpc_new_module_name'] ?? '')),
            'tpc_existing_module_name' => trim((string) ($p['tpc_existing_module_name'] ?? '')),
        ];

        $form = $this->form('melistemplatingplugincreator_step1_form');
        $form->setData($data);

        // Le champ non concerné par la destination choisie ne doit pas être validé (idem legacy).
        $filter = $form->getInputFilter();
        if ($data['tpc_plugin_destination'] === self::NEW_MODULE) {
            $filter->remove('tpc_existing_module_name');
        } elseif ($data['tpc_plugin_destination'] !== '') {
            $filter->remove('tpc_new_module_name');
        } else {
            $filter->remove('tpc_new_module_name');
            $filter->remove('tpc_existing_module_name');
        }

        if (!$form->isValid()) {
            return ['valid' => false, 'errors' => $this->formErrors($form)];
        }

        $sm         = $this->getServiceManager();
        $translator = $sm->get('translator');
        $errors     = [];

        if ($data['tpc_plugin_destination'] === self::NEW_MODULE && $data['tpc_new_module_name'] !== '') {
            $newModule = strtolower($data['tpc_new_module_name']);

            $reserved = (array) $sm->get('MelisCoreConfig')->getItem('melistemplatingplugincreator/datas/reserved_keywords');
            if (in_array($newModule, $reserved, true)) {
                $errors['tpc_new_module_name'] = $this->fieldError($form, 'tpc_new_module_name', sprintf(
                    $translator->translate('tr_melistemplatingplugincreator_err_module_name_reserved_keyword'),
                    $data['tpc_new_module_name'],
                ));
            }

            $existing = array_map('strtolower', array_merge(
                (array) $sm->get('ModulesService')->getModulePlugins(),
                (array) $sm->get('ModulesService')->getAllModules(),
                (array) $sm->get('MelisAssetManagerModulesService')->getSitesModules(),
            ));
            if (in_array($newModule, $existing, true)) {
                $errors['tpc_new_module_name'] = $this->fieldError($form, 'tpc_new_module_name', sprintf(
                    $translator->translate('tr_melistemplatingplugincreator_err_module_exist'),
                    $data['tpc_new_module_name'],
                ));
            }
        }

        if ($data['tpc_plugin_destination'] !== self::NEW_MODULE && $data['tpc_existing_module_name'] !== '') {
            $pluginKey = strtolower($data['tpc_existing_module_name'] . $data['tpc_plugin_name'] . 'plugin');
            $taken     = array_map('strtolower', (array) $this->tpcService()->getSiteTemplatingPluginNames($data['tpc_existing_module_name']));
            if ($taken && in_array($pluginKey, $taken, true)) {
                $errors['tpc_plugin_name'] = $this->fieldError($form, 'tpc_plugin_name', sprintf(
                    $translator->translate('tr_melistemplatingplugincreator_err_plugin_name_exist'),
                    $data['tpc_plugin_name'],
                ));
            }
        }

        if ($errors) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Le champ non retenu est vidé : `getDestinationModule()` (legacy) ne lit que celui de la destination.
        $state = $this->state();
        $state['step_1'] = $form->getData();
        // Changer de destination/nom invalide le `template_path` déjà calculé à l'étape 3.
        if (($state['step_3']['tab_' . self::TAB]['field_1']['tpc_field_default_value'] ?? null) !== null
            && $this->templatePath($state['step_1']) !== $state['step_3']['tab_' . self::TAB]['field_1']['tpc_field_default_value']) {
            unset($state['step_3'], $state['step_4']);
        }
        $this->setState($state);

        return ['valid' => true, 'errors' => (object) []];
    }

    /**
     * Étape 2 — titre + description par langue, et vignette (déjà téléversée via /tpc/thumbnail).
     * Règle legacy : l'étape passe dès qu'UNE langue est valide (les autres restent facultatives).
     * La vignette est obligatoire — le générateur la copie dans les assets du plugin.
     */
    private function saveStep2(array $p): array
    {
        $langs  = $this->languages();
        $posted = (array) ($p['languages'] ?? []);
        $errors = [];
        $saved  = [];
        $valid  = 0;

        foreach ($langs as $lang) {
            $locale = $lang['locale'];
            $form   = $this->form('melistemplatingplugincreator_step2_form1');
            $form->setData([
                'tpc_plugin_title' => trim((string) ($posted[$locale]['tpc_plugin_title'] ?? '')),
                'tpc_plugin_desc'  => trim((string) ($posted[$locale]['tpc_plugin_desc'] ?? '')),
                'tpc_lang_local'   => $locale,
            ]);
            if ($form->isValid()) {
                $valid++;
                $saved[$locale] = $form->getData();
            } else {
                $errors[$locale] = $this->formErrors($form);
            }
        }

        $state = $this->state();
        if (empty($state['step_2']['plugin_thumbnail'])) {
            return ['valid' => false, 'errors' => [
                'tpc_plugin_upload_thumbnail' => [
                    'label'    => $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_upload_thumbnail'),
                    'messages' => [$this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_err_empty')],
                ],
            ]];
        }

        if (!$valid) {
            return ['valid' => false, 'errors' => $errors];
        }

        // Au moins une langue valide ⇒ étape valide (les blocs invalides ne sont pas enregistrés).
        $thumbnail = $state['step_2']['plugin_thumbnail'];
        $state['step_2'] = $saved + ['plugin_thumbnail' => $thumbnail];
        $this->setState($state);

        return ['valid' => true, 'errors' => (object) []];
    }

    /**
     * Étape 3 — nombre de champs + spécification de chaque champ.
     * Le champ 1 est TOUJOURS `template_path` : il est recalculé côté serveur (jamais lu du client).
     */
    private function saveStep3(array $p): array
    {
        $state = $this->state();
        if (empty($state['step_1'])) {
            return ['valid' => false, 'errors' => ['__step' => ['label' => '', 'messages' => ['Step 1 is required']]]];
        }

        $count = (string) ($p['tpc_main_property_field_count'] ?? '');
        $main  = $this->form('melistemplatingplugincreator_step3_form1');
        $main->setData([
            'tpc_main_property_field_count' => $count,
            'tpc_property_tab_number'       => (string) self::TAB,
        ]);
        if (!$main->isValid()) {
            return ['valid' => false, 'errors' => ['main' => $this->formErrors($main)]];
        }

        $count  = (int) $count;
        $posted = array_values((array) ($p['fields'] ?? []));
        $errors = [];
        $fields = [];
        $names  = [];
        $tpl    = $this->templatePath($state['step_1']);

        for ($i = 1; $i <= $count; $i++) {
            // Champ 1 : verrouillé, aucune validation (valeurs prédéfinies) — comme le legacy.
            if ($i === 1) {
                $fields['field_1'] = [
                    'tpc_field_name'            => 'template_path',
                    'tpc_field_display_type'    => self::DROPDOWN,
                    'tpc_field_is_required'     => '1',
                    'tpc_field_default_options' => $tpl,
                    'tpc_field_default_value'   => $tpl,
                ];
                $names['template_path'] = 1;
                continue;
            }

            $row  = (array) ($posted[$i - 1] ?? []);
            $type = (string) ($row['tpc_field_display_type'] ?? '');
            $data = [
                'tpc_field_name'            => trim((string) ($row['tpc_field_name'] ?? '')),
                'tpc_field_display_type'    => $type,
                'tpc_field_is_required'     => (string) ($row['tpc_field_is_required'] ?? ''),
                'tpc_field_default_options' => (string) ($row['tpc_field_default_options'] ?? ''),
                'tpc_field_default_value'   => (string) ($row['tpc_field_default_value'] ?? ''),
            ];

            $form = $this->form('melistemplatingplugincreator_step3_field_form');
            // Valeur par défaut numérique attendue pour NumericInput / PageInput (idem legacy).
            if (in_array($type, self::NUMERIC_TYPES, true) && $data['tpc_field_default_value'] !== '') {
                $form->getInputFilter()->add([
                    'name'       => 'tpc_field_default_value',
                    'required'   => false,
                    'validators' => [[
                        'name'    => 'Digits',
                        'options' => ['messages' => [
                            \Laminas\Validator\Digits::NOT_DIGITS
                                => $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_digits_only'),
                        ]],
                    ]],
                ]);
            }
            $form->setData($data);

            if (!$form->isValid()) {
                $errors['field_' . $i] = $this->formErrors($form);
                continue;
            }

            $clean = $form->getData();

            // Un Dropdown sans option est inexploitable (le générateur écrit ses value_options).
            if ($type === self::DROPDOWN && trim($clean['tpc_field_default_options']) === '') {
                $errors['field_' . $i]['tpc_field_default_options'] = $this->fieldError(
                    $form, 'tpc_field_default_options',
                    $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_err_empty'),
                );
                continue;
            }

            // Doublon de nom de champ (règle legacy `DuplicateFieldName`). Le message attend le nom
            // fautif ET le numéro du champ qui le porte déjà.
            $name = $clean['tpc_field_name'];
            if ($name !== '' && isset($names[$name])) {
                $errors['field_' . $i]['tpc_field_name'] = $this->fieldError(
                    $form, 'tpc_field_name',
                    sprintf(
                        $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_tpc_field_name_exist'),
                        $name,
                        $names[$name],
                    ),
                );
                continue;
            }
            $names[$name] = $i;
            $fields['field_' . $i] = $clean;
        }

        if ($errors) {
            return ['valid' => false, 'errors' => $errors];
        }

        $state['step_3'] = [
            'main_form' => [
                'tpc_main_property_field_count' => (string) $count,
                'tpc_property_tab_number'       => (string) self::TAB,
            ],
            'tab_' . self::TAB => $fields,
        ];
        // Les traductions de l'étape 4 dépendent des champs : elles sont invalidées ici.
        unset($state['step_4']);
        $this->setState($state);

        return ['valid' => true, 'errors' => (object) []];
    }

    /**
     * Étape 4 — libellé + infobulle de chaque champ, par langue (+ un libellé par option de Dropdown).
     * Règle legacy : un champ est valide dès qu'UNE langue le renseigne complètement.
     */
    private function saveStep4(array $p): array
    {
        $state  = $this->state();
        $fields = (array) ($state['step_3']['tab_' . self::TAB] ?? []);
        if (!$fields) {
            return ['valid' => false, 'errors' => ['__step' => ['label' => '', 'messages' => ['Step 3 is required']]]];
        }

        $langs  = $this->languages();
        $posted = (array) ($p['translations'] ?? []);
        $errors = [];
        $saved  = [];

        foreach ($fields as $fieldKey => $field) {
            $num        = (int) substr($fieldKey, strlen('field_'));
            $optionKeys = $this->optionLabelKeys($field);
            $fieldValid = 0;
            $fieldErr   = [];

            foreach ($langs as $lang) {
                $locale = $lang['locale'];
                $row    = (array) ($posted[$locale][$fieldKey] ?? []);

                $form = $this->form('melistemplatingplugincreator_step4');
                $data = [
                    'tpc_lang_local'    => $locale,
                    'tpc_tab_num'       => (string) self::TAB,
                    'tpc_field_num'     => (string) $num,
                    'tpc_field_name'    => (string) $field['tpc_field_name'],
                    'tpc_field_label'   => trim((string) ($row['tpc_field_label'] ?? '')),
                    'tpc_field_tooltip' => trim((string) ($row['tpc_field_tooltip'] ?? '')),
                ];
                // Un libellé obligatoire par option de Dropdown (cf. setDropdownValueTranslation).
                foreach ($optionKeys as $key => $optionValue) {
                    $this->addRequiredText($form, $key, $optionValue);
                    $data[$key] = trim((string) ($row[$key] ?? ''));
                }
                $form->setData($data);

                if ($form->isValid()) {
                    $fieldValid++;
                    $saved[$locale][$fieldKey] = $form->getData();
                } else {
                    $fieldErr[$locale] = $this->formErrors($form);
                }
            }

            if (!$fieldValid) {
                $errors[$fieldKey] = $fieldErr;
            }
        }

        if ($errors) {
            return ['valid' => false, 'errors' => $errors];
        }

        $step4 = [];
        foreach ($saved as $locale => $rows) {
            $step4[$locale]['tab_' . self::TAB] = $rows;
        }
        $state['step_4'] = $step4;
        $this->setState($state);

        return ['valid' => true, 'errors' => (object) []];
    }

    /**
     * Champs à traduire à l'étape 4, dérivés de l'étape 3 (nom, type, et la liste des options de
     * Dropdown avec la CLÉ de libellé exacte attendue par le générateur, `removeNonAlphaNumeric()`).
     * La brique ne recalcule donc jamais cette clé côté JS.
     */
    public function translationFieldsAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('wizard')) { return $deny; }
        try {
            $state  = $this->state();
            $fields = [];
            foreach ((array) ($state['step_3']['tab_' . self::TAB] ?? []) as $key => $field) {
                $options = [];
                foreach ($this->optionLabelKeys($field) as $labelKey => $optionValue) {
                    $options[] = ['key' => $labelKey, 'value' => $optionValue];
                }
                $fields[] = [
                    'key'         => $key,
                    'num'         => (int) substr($key, strlen('field_')),
                    'name'        => (string) $field['tpc_field_name'],
                    'displayType' => (string) $field['tpc_field_display_type'],
                    'locked'      => $key === 'field_1',
                    'options'     => $options,
                ];
            }
            return $this->ok(['fields' => $fields, 'languages' => $this->languages()]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    // ════════════════════════════════════════════════════════════════════════
    //  VIGNETTE (étape 2)
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Téléverse la vignette du plugin. Mêmes contraintes que le legacy (taille min/max de la
     * config, contenu réellement décodable en image), plus deux durcissements : liste blanche
     * d'extensions et nom de fichier assaini (le legacy ne retirait que les accents/espaces),
     * dossier en 0755 (le legacy faisait `mkdir(0755)` + `chmod(0777)`).
     */
    public function thumbnailAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('thumbnail')) { return $deny; }
        if ($deny = $this->denyUnlessCan('thumbnail.create')) { return $deny; }
        try {
            $translator = $this->getServiceManager()->get('translator');
            $files      = $this->getRequest()->getFiles()->toArray();
            $file       = $files['tpc_plugin_upload_thumbnail'] ?? $files['file'] ?? null;

            if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'] ?? '')) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_err_empty'));
            }

            $cfg     = (array) $this->getServiceManager()->get('MelisCoreConfig')->getItem('melistemplatingplugincreator/datas/plugin_thumbnail');
            $minSize = (int) ($cfg['min_size'] ?? 1);
            $maxSize = (int) ($cfg['max_size'] ?? 512000);
            $size    = (int) ($file['size'] ?? 0);

            if ($size < $minSize || $size > $maxSize) {
                return $this->bad(sprintf($translator->translate('tr_melistemplatingplugincreator_upload_too_big'), $this->formatBytes($maxSize)));
            }

            // Le contenu doit être une image (le legacy sniffe avec GD ; on garde ce contrôle) ET
            // porter une extension autorisée, cohérente avec le type réel.
            $info = @getimagesize($file['tmp_name']);
            $ext  = match ($info[2] ?? null) {
                IMAGETYPE_GIF  => 'gif',
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG  => 'png',
                default        => null,
            };
            if ($ext === null) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_save_upload_image_imageFalseType'));
            }

            $base = pathinfo((string) ($file['name'] ?? 'thumbnail'), PATHINFO_FILENAME);
            $base = $this->tpcService()->removeAccents(str_replace(' ', '_', trim($base)));
            $base = preg_replace('/[^A-Za-z0-9_-]/', '', $base) ?: 'thumbnail';

            $dir = $this->thumbnailDir();
            if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_fp_temp_thumbnail'));
            }
            // Une seule vignette par assistant : on purge les précédentes.
            foreach ((array) glob($dir . '/*') as $old) { @unlink($old); }

            $target = $dir . '/' . $base . '.' . $ext;
            if (!@move_uploaded_file($file['tmp_name'], $target)) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_fp_temp_thumbnail'));
            }
            @chmod($target, 0644);

            // Chemin WEB (c'est ce que lit le générateur pour retrouver l'extension + le fichier).
            $webPath = '/tpc/temp-thumbnail/' . $this->sessionId() . '/' . $base . '.' . $ext;
            $state   = $this->state();
            $state['step_2']['plugin_thumbnail'] = $webPath;
            $this->setState($state);

            return $this->ok(['thumbnail' => $webPath]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /**
     * Retire la vignette. Le legacy ne vidait QUE la clé de session et laissait le fichier orphelin
     * sur disque (dans un dossier servi par le serveur web) : on supprime aussi le fichier.
     */
    public function thumbnailRemoveAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('thumbnail')) { return $deny; }
        if ($deny = $this->denyUnlessCan('thumbnail.delete')) { return $deny; }
        try {
            $this->deleteThumbnailDir();
            $state = $this->state();
            unset($state['step_2']['plugin_thumbnail']);
            $this->setState($state);
            return $this->ok(['thumbnail' => null]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    // ════════════════════════════════════════════════════════════════════════
    //  RÉCAPITULATIF (étape 5) + GÉNÉRATION (étape 6)
    // ════════════════════════════════════════════════════════════════════════

    /** Récapitulatif lisible des étapes 1 → 4 (l'étape 5 legacy est un simple rendu en lecture seule). */
    public function summaryAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('summary')) { return $deny; }
        if ($deny = $this->denyUnlessCan('summary.list')) { return $deny; }
        try {
            $state = $this->state();
            return $this->ok($this->publicState() + [   // publicState() porte déjà `templatePath`
                'languages'    => $this->languages(),
                'targetModule' => empty($state['step_1']) ? null : $this->destinationModule($state['step_1']),
            ]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /**
     * Étape 6 — GÉNÈRE le plugin. Transcription fidèle de `processStep6()` :
     *   • branche « nouveau module » : amorce le conteneur `melistoolcreator`, appelle
     *     MelisToolCreatorService::createTool() (échafaude le module), puis génère le plugin,
     *     inscrit le module dans le `module.load.php` du site choisi, l'active, et invalide le
     *     cache de chemins de modules ;
     *   • branche « module existant » : génère simplement le plugin dans ce module.
     *
     * Les services legacy écrivent sur le disque et peuvent émettre des warnings PHP (mkdir/copy)
     * directement sur la sortie : on les capture (`ob_start`) pour ne pas corrompre le JSON.
     */
    public function generateAction(): HttpResponse
    {
        if ($deny = $this->denyUnlessAccess()) { return $deny; }
        if ($deny = $this->denyUnlessCan('finalization')) { return $deny; }
        if ($deny = $this->denyUnlessCan('finalization.create')) { return $deny; }
        try {
            $sm         = $this->getServiceManager();
            $translator = $sm->get('translator');
            $state      = $this->state();

            // L'assistant doit être complet : on ne génère jamais depuis un état partiel.
            foreach (['step_1', 'step_2', 'step_3', 'step_4'] as $required) {
                if (empty($state[$required])) {
                    return $this->bad($translator->translate('tr_melistemplatingplugincreator_err_message'));
                }
            }
            if (empty($state['step_2']['plugin_thumbnail'])) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_err_message'));
            }

            $payload  = $this->jsonBody();
            $siteName = trim((string) ($payload['tpc_existing_site_name'] ?? ''));   // '' = aucun site
            $activate = !empty($payload['tpc_activate_plugin']);
            $isNew    = ($state['step_1']['tpc_plugin_destination'] ?? '') === self::NEW_MODULE;

            // Le site doit exister (le legacy ne validait pas ce champ du tout).
            if ($siteName !== '' && !in_array($siteName, array_column($this->sites(), 'name'), true)) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_err_message'));
            }

            $notices = [];
            ob_start();
            try {
                if ($isNew) {
                    // MelisToolCreator échafaude le module vide que le plugin viendra remplir.
                    $toolContainer = new Container('melistoolcreator');
                    $toolContainer['melis-toolcreator'] = ['step1' => [
                        'tcf-name'           => $state['step_1']['tpc_new_module_name'],
                        'tcf-tool-type'      => 'blank',
                        'tcf-tool-edit-type' => 'modal',
                    ]];
                    $toolCreatorSrv = $sm->get('MelisToolCreatorService');
                    $toolCreatorSrv->createTool();
                }

                // ⚠ après l'écriture de session : le service snapshot l'état dans son constructeur.
                $generated = (bool) $this->tpcService()->generateTemplatingPlugin();

                if ($generated && $isNew) {
                    if ($siteName !== '') {
                        $notices = array_merge($notices, $this->attachModuleToSite($state, $siteName));
                    }
                    $sm->get('ModulesService')->activateModule($toolCreatorSrv->moduleName());

                    // Force la reconstruction du cache de chemins de modules.
                    $pathCache = $_SERVER['DOCUMENT_ROOT'] . '/../config/melis.modules.path.php';
                    if (file_exists($pathCache)) { @unlink($pathCache); }
                    unset($toolContainer['melis-toolcreator']);
                }
            } finally {
                ob_end_clean();
            }

            if (empty($generated)) {
                return $this->bad($translator->translate('tr_melistemplatingplugincreator_generate_plugin_error_encountered'));
            }

            $pluginName   = $state['step_1']['tpc_plugin_name'];
            $targetModule = $this->destinationModule($state['step_1']);

            // Succès : l'assistant repart de zéro (le générateur a déjà purgé la vignette temporaire).
            $this->setState(['sessionID' => $this->sessionId()]);

            return $this->ok([
                'generated'       => true,
                'module'          => $targetModule,
                'plugin'          => $pluginName,
                'site'            => $siteName ?: null,
                'restartRequired' => $activate,   // la brique recharge la plateforme
                'notices'         => $notices,
            ]);
        } catch (\Throwable $e) { return $this->errorResponse($e); }
    }

    /**
     * Inscrit le module fraîchement créé dans le `config/module.load.php` du module de site choisi
     * (repris de `processStep6`). Renvoie un avertissement non bloquant si le fichier n'est pas
     * inscriptible : le plugin EST généré, seule l'activation sur le site a échoué.
     */
    private function attachModuleToSite(array $state, string $siteName): array
    {
        $sm      = $this->getServiceManager();
        $modules = $sm->get('ModulesService');

        // Défense en profondeur : `generateAction()` a déjà vérifié que $siteName appartient à la
        // liste blanche des sites, mais ce nom finit dans un chemin `include` — on le re-contraint
        // ici à un identifiant de module nu (ni séparateur, ni `..`), pour que cette méthode reste
        // sûre quel que soit l'appelant.
        if ($siteName === '' || !preg_match('/^[A-Za-z0-9_]+$/', $siteName)) {
            return [$sm->get('translator')->translate('tr_melistemplatingplugincreator_fp_config')];
        }

        $modulePath = $modules->getComposerModulePath($siteName)
            ?: $_SERVER['DOCUMENT_ROOT'] . '/../module/MelisSites/' . $siteName;
        $filePath = $modulePath . '/config/module.load.php';

        if (!file_exists($filePath) || !is_writable($filePath)) {
            return [$sm->get('translator')->translate('tr_melistemplatingplugincreator_fp_config')];
        }

        $siteModules   = (array) include $filePath;
        $siteModules[] = $this->tpcService()->generateModuleNameCase($state['step_1']['tpc_new_module_name']);
        $sm->get('MelisCmsSiteModuleLoadService')->createModuleLoader($filePath, array_values(array_unique($siteModules)));

        return [];
    }

    // ════════════════════════════════════════════════════════════════════════
    //  HELPERS — session / formulaires / données de référence
    // ════════════════════════════════════════════════════════════════════════

    /** Instancie un formulaire de l'outil depuis sa config `app.tools.php` (mêmes validateurs). */
    private function form(string $key): \Laminas\Form\Form
    {
        $factory = new FormFactory();
        $factory->setFormElementManager($this->getServiceManager()->get('FormElementManager'));
        $config = $this->getServiceManager()->get('MelisCoreConfig')
            ->getFormMergedAndOrdered('melistemplatingplugincreator/forms/' . $key, $key);

        return $factory->createForm($config);
    }

    /**
     * Ajoute un élément texte obligatoire pour le libellé d'une option de Dropdown (étape 4).
     * Le libellé reprend le format legacy (`setDropdownValueTranslation`) : « Label for "<option>" ».
     */
    private function addRequiredText(\Laminas\Form\Form $form, string $name, string $optionValue): void
    {
        $element = new \Laminas\Form\Element\Text($name);
        $element->setLabel(sprintf(
            $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_tpc_dropdown_value_label'),
            $optionValue,
        ));
        $form->add($element);
        $form->getInputFilter()->add([
            'name'       => $name,
            'required'   => true,
            'validators' => [[
                'name'    => 'NotEmpty',
                'options' => ['messages' => [
                    \Laminas\Validator\NotEmpty::IS_EMPTY
                        => $this->getServiceManager()->get('translator')->translate('tr_melistemplatingplugincreator_err_empty'),
                ]],
            ]],
        ]);
    }

    /**
     * Clés de libellé attendues pour les options d'un champ Dropdown : `removeNonAlphaNumeric($opt).'_label'`.
     * Retour : `[ cléDeLibellé => valeurDeLOption ]`. Vide pour tout autre type d'affichage.
     */
    private function optionLabelKeys(array $field): array
    {
        if (($field['tpc_field_display_type'] ?? '') !== self::DROPDOWN) {
            return [];
        }
        $out = [];
        foreach (explode(',', (string) ($field['tpc_field_default_options'] ?? '')) as $option) {
            if (trim($option) === '') { continue; }
            $out[$this->tpcService()->removeNonAlphaNumeric($option) . '_label'] = $option;
        }
        return $out;
    }

    /** Module de destination : le nouveau (normalisé) ou le module de site existant. */
    private function destinationModule(array $step1): string
    {
        return ($step1['tpc_plugin_destination'] ?? '') === self::NEW_MODULE
            ? (string) $this->tpcService()->generateModuleNameCase($step1['tpc_new_module_name'] ?? '')
            : (string) ($step1['tpc_existing_module_name'] ?? '');
    }

    /** Valeur imposée du champ 1 : `<Module>/plugins/<plugin-view-name>`. */
    private function templatePath(array $step1): string
    {
        return $this->destinationModule($step1) . '/plugins/'
            . $this->tpcService()->convertToViewName($step1['tpc_plugin_name'] ?? '');
    }

    /**
     * ⚠ N'appeler qu'après avoir écrit l'état de session de la requête : le service en prend un
     * instantané dans son constructeur et le ServiceManager met l'instance en cache.
     */
    private function tpcService(): \MelisTemplatingPluginCreator\Service\MelisTemplatingPluginCreatorService
    {
        return $this->getServiceManager()->get('MelisTemplatingPluginCreatorService');
    }

    /** Langues de la plateforme, locale courante en tête (comme `getOrderedLanguagesByCurrentLocale`). */
    private function languages(): array
    {
        $rows    = $this->getServiceManager()->get('MelisCoreTableLang')->fetchAll()->toArray();
        $current = $this->currentLocale();

        $langs = array_map(static fn ($r) => [
            'id'     => (int) $r['lang_id'],
            'locale' => (string) $r['lang_locale'],
            'name'   => (string) $r['lang_name'],
        ], $rows);

        usort($langs, static fn ($a, $b) => ($b['locale'] === $current ? 1 : 0) <=> ($a['locale'] === $current ? 1 : 0));

        return $langs;
    }

    private function currentLocale(): string
    {
        $c = new Container('meliscore');
        return (string) ($c['melis-lang-locale'] ?? 'en_EN');
    }

    /** Sites du CMS, hors sites de démonstration (même exclusion que le select legacy). */
    private function sites(): array
    {
        $sites = [];
        foreach ((array) $this->getServiceManager()->get('MelisCmsSiteService')->getAllSites() as $site) {
            if (in_array($site['site_name'], ['MelisDemoCms', 'MelisDemoCmsTwig'], true)) { continue; }
            $sites[] = ['name' => (string) $site['site_name'], 'label' => (string) $site['site_label']];
        }
        return $sites;
    }

    // ── État de session (partagé avec le contrôleur legacy) ──────────────────

    private function state(): array
    {
        $c = new Container(self::SESSION_NS);
        return (array) ($c[self::SESSION_ROOT] ?? []);
    }

    /** Écriture explicite (lire → muter → réécrire) : `$c['root']['step_1'] = …` ne persiste pas. */
    private function setState(array $state): void
    {
        $state['sessionID'] = $state['sessionID'] ?? $this->sessionId();
        $c = new Container(self::SESSION_NS);
        $c[self::SESSION_ROOT] = $state;
    }

    private function sessionId(): string
    {
        $state = (new Container(self::SESSION_NS))[self::SESSION_ROOT] ?? [];
        return (string) ($state['sessionID'] ?? (new Container(self::SESSION_NS))->getManager()->getId());
    }

    /**
     * État exposé à la brique, NORMALISÉ : la forme interne de la session (`tab_1`, `field_N`,
     * `main_form`) est un détail du générateur legacy — la brique reçoit des tableaux/objets plats
     * et n'a jamais à connaître ces clés. `completedStep` permet de reprendre l'assistant là où
     * l'utilisateur l'avait laissé après un rechargement de page.
     */
    private function publicState(): array
    {
        $state  = $this->state();
        $fields = [];
        foreach ((array) ($state['step_3']['tab_' . self::TAB] ?? []) as $field) {
            $fields[] = [
                'tpc_field_name'            => (string) ($field['tpc_field_name'] ?? ''),
                'tpc_field_display_type'    => (string) ($field['tpc_field_display_type'] ?? ''),
                'tpc_field_is_required'     => (string) ($field['tpc_field_is_required'] ?? '0'),
                'tpc_field_default_options' => (string) ($field['tpc_field_default_options'] ?? ''),
                'tpc_field_default_value'   => (string) ($field['tpc_field_default_value'] ?? ''),
            ];
        }

        $translations = [];
        foreach ((array) ($state['step_4'] ?? []) as $locale => $tabs) {
            $translations[$locale] = (object) ($tabs['tab_' . self::TAB] ?? []);
        }

        $completed = 0;
        foreach (['step_1', 'step_2', 'step_3', 'step_4'] as $i => $key) {
            if (!empty($state[$key])) { $completed = $i + 1; }
        }

        return [
            'step1'         => (object) ($state['step_1'] ?? []),
            'step2'         => (object) array_diff_key((array) ($state['step_2'] ?? []), ['plugin_thumbnail' => 1]),
            'step3'         => ['fieldCount' => count($fields), 'fields' => $fields],
            'step4'         => (object) $translations,
            'thumbnail'     => $state['step_2']['plugin_thumbnail'] ?? null,
            'completedStep' => $completed,
            // Valeur imposée du champ `template_path`. Calculée ICI (generateModuleNameCase +
            // convertToViewName) : la brique l'AFFICHE, elle ne réimplémente pas ces règles en JS.
            'templatePath'  => empty($state['step_1']) ? null : $this->templatePath($state['step_1']),
        ];
    }

    private function thumbnailDir(): string
    {
        $cfg = (array) $this->getServiceManager()->get('MelisCoreConfig')->getItem('melistemplatingplugincreator/datas/plugin_thumbnail');
        return rtrim((string) ($cfg['path'] ?? ''), '/') . '/' . $this->sessionId();
    }

    private function deleteThumbnailDir(): void
    {
        $dir = $this->thumbnailDir();
        if (!is_dir($dir)) { return; }
        foreach ((array) glob($dir . '/*') as $file) { @unlink($file); }
        @rmdir($dir);
    }

    // ── Erreurs de formulaire ────────────────────────────────────────────────

    /**
     * Aplati les messages Laminas en `{ champ: { label, messages: [texte…] } }`.
     * Les validateurs de `app.tools.php` portent des CLÉS `tr_…` : on les traduit ici (le legacy
     * les renvoyait brutes au JS).
     */
    private function formErrors(\Laminas\Form\Form $form): array
    {
        $translator = $this->getServiceManager()->get('translator');
        $out        = [];

        foreach ($form->getMessages() as $field => $messages) {
            $texts = [];
            foreach ((array) $messages as $message) {
                $texts[] = is_string($message) ? $translator->translate($message) : (string) json_encode($message);
            }
            $out[$field] = [
                'label'    => $form->has($field) ? (string) $form->get($field)->getLabel() : '',
                'messages' => $texts,
            ];
        }
        return $out;
    }

    /** Erreur métier ponctuelle attachée à un champ (message déjà traduit). */
    private function fieldError(\Laminas\Form\Form $form, string $field, string $message): array
    {
        return [
            'label'    => $form->has($field) ? (string) $form->get($field)->getLabel() : '',
            'messages' => [$message],
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'kB', 'MB', 'GB'];
        $i     = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $i     = min($i, count($units) - 1);
        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }

    // ── Socle HTTP / droits (gabarit MelisReactApi) ──────────────────────────

    private function isAuthenticated(): bool
    {
        return $this->getServiceManager()->get('MelisCoreAuth')->hasIdentity();
    }

    private function denyUnlessAccess(): ?HttpResponse
    {
        if (!$this->isAuthenticated()) {
            return $this->jsonResponse(['success' => false, 'error' => 'Unauthenticated'], 401);
        }
        try {
            if (!$this->getServiceManager()->get('MelisCoreRights')->canAccess(self::MELIS_KEY)) {
                return $this->jsonResponse(['success' => false, 'error' => 'Forbidden'], 403);
            }
        } catch (\Throwable) {}
        return null;
    }

    /** Corps JSON de la requête (les endpoints d'upload lisent `getFiles()` à la place). */
    private function jsonBody(): array
    {
        $raw = (string) $this->getRequest()->getContent();
        if ($raw === '') { return []; }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function ok($data, int $status = 200): HttpResponse
    {
        return $this->jsonResponse(['success' => true, 'data' => $data], $status);
    }

    private function bad(string $message): HttpResponse
    {
        return $this->jsonResponse(['success' => false, 'error' => $message], 400);
    }

    private function jsonResponse(array $data, int $status = 200): HttpResponse
    {
        /** @var HttpResponse $response */
        $response = $this->getResponse();
        $response->setStatusCode($status);
        $response->getHeaders()->addHeaders([
            'Content-Type'           => 'application/json; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'no-store',
        ]);
        $response->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return $response;
    }

    private function errorResponse(\Throwable $e, int $status = 500): HttpResponse
    {
        return $this->jsonResponse([
            'success' => false,
            'error'   => $e->getMessage(),
            'file'    => basename($e->getFile()) . ':' . $e->getLine(),
        ], $status);
    }
}
