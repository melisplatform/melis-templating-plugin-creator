/**
 * Client typé des endpoints `/melis/react-api/tpc/*` (contrôleur du module).
 *
 * Le back-office Melis exige l'en-tête `X-Requested-With` sur ses routes JSON. Le contrat est
 * toujours `{ success, data, error }` : `apiFetch` lève sur `success:false` (erreur technique ou
 * refus de droits) — MAIS une VALIDATION échouée n'est pas une erreur : elle revient en
 * `data.valid:false` + `data.errors`, que l'appelant affiche champ par champ.
 */

const XHR: HeadersInit = { 'X-Requested-With': 'XMLHttpRequest' }
const BASE = '/melis/react-api/tpc'

interface Envelope<T> { success: boolean; data: T; error?: string }

async function apiFetch<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await fetch(`${BASE}${path}`, {
    credentials: 'same-origin',
    ...init,
    headers: { ...XHR, ...(init?.headers ?? {}) },
  })
  let body: Envelope<T>
  try {
    body = (await res.json()) as Envelope<T>
  } catch {
    throw new Error(`HTTP ${res.status}`)
  }
  if (!body.success) throw new Error(body.error || `HTTP ${res.status}`)
  return body.data
}

const postJson = <T>(path: string, payload: unknown) =>
  apiFetch<T>(path, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })

// ─── Types ───────────────────────────────────────────────────────────────────

export interface Language { id: number; locale: string; name: string }
export interface StepMeta { key: string; name: string; icon: string }
export interface Site { name: string; label: string }
export interface DisplayType { value: string; label: string }

export interface Context {
  /** Non vide ⇒ l'environnement interdit la génération (droits FS, GD manquant) : on n'affiche que ça. */
  blocking: string[]
  steps: StepMeta[]
  languages: Language[]
  currentLocale: string
  siteModules: string[]
  sites: Site[]
  displayTypes: DisplayType[]
  maxFields: number
  thumbnail: { minSize: number; maxSize: number; accept: string }
}

/** Spécification d'un champ (étape 3) — mêmes clés que la session legacy, aucune traduction. */
export interface FieldSpec {
  tpc_field_name: string
  tpc_field_display_type: string
  tpc_field_is_required: string
  tpc_field_default_options: string
  tpc_field_default_value: string
}

export interface Step1Data {
  tpc_plugin_name?: string
  tpc_plugin_destination?: string
  tpc_new_module_name?: string
  tpc_existing_module_name?: string
}
export interface LangTexts { tpc_plugin_title?: string; tpc_plugin_desc?: string }
/** `field_N` → { tpc_field_label, tpc_field_tooltip, <option>_label… } */
export type TranslationRow = Record<string, string>

export interface WizardState {
  step1: Step1Data
  step2: Record<string, LangTexts>
  step3: { fieldCount: number; fields: FieldSpec[] }
  step4: Record<string, Record<string, TranslationRow>>
  thumbnail: string | null
  /** Dernière étape enregistrée (0..4) → reprise après rechargement. */
  completedStep: number
  /** Valeur imposée du champ `template_path`, calculée côté serveur (null tant que l'étape 1 n'est pas enregistrée). */
  templatePath: string | null
}

/** `{ champ: { label, messages[] } }`, éventuellement imbriqué (par langue / par champ). */
export type FieldErrors = Record<string, { label: string; messages: string[] }>
export type StepErrors = Record<string, unknown>
export interface StepResult { valid: boolean; errors: StepErrors }

export interface TranslationField {
  key: string
  num: number
  name: string
  displayType: string
  locked: boolean
  options: { key: string; value: string }[]
}

export interface Summary extends WizardState {
  languages: Language[]
  targetModule: string | null
}

export interface GenerateResult {
  generated: boolean
  module: string
  plugin: string
  site: string | null
  /** L'utilisateur a coché « activer le plugin » ⇒ la plateforme doit être rechargée. */
  restartRequired: boolean
  /** Avertissements NON bloquants (ex. module.load.php du site non inscriptible). */
  notices: string[]
}

// ─── Endpoints ───────────────────────────────────────────────────────────────

export const fetchContext = () => apiFetch<Context>('/context')
export const fetchState = () => apiFetch<WizardState>('/state')
export const resetWizard = () => postJson<WizardState>('/reset', {})

export const saveStep1 = (d: Step1Data) => postJson<StepResult>('/step/1', d)
export const saveStep2 = (languages: Record<string, LangTexts>) => postJson<StepResult>('/step/2', { languages })
export const saveStep3 = (fieldCount: number, fields: FieldSpec[]) =>
  postJson<StepResult>('/step/3', { tpc_main_property_field_count: String(fieldCount), fields })
export const saveStep4 = (translations: Record<string, Record<string, TranslationRow>>) =>
  postJson<StepResult>('/step/4', { translations })

export const fetchTranslationFields = () =>
  apiFetch<{ fields: TranslationField[]; languages: Language[] }>('/translation-fields')

export const fetchSummary = () => apiFetch<Summary>('/summary')

export const generatePlugin = (siteName: string, activate: boolean) =>
  postJson<GenerateResult>('/generate', { tpc_existing_site_name: siteName, tpc_activate_plugin: activate })

export async function uploadThumbnail(file: File): Promise<string> {
  const fd = new FormData()
  fd.append('tpc_plugin_upload_thumbnail', file)
  const data = await apiFetch<{ thumbnail: string }>('/thumbnail', { method: 'POST', body: fd })
  return data.thumbnail
}

export const removeThumbnail = () => postJson<{ thumbnail: null }>('/thumbnail/remove', {})
