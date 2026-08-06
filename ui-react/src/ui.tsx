/**
 * Primitives partagées par la brique Templating Plugin Creator (styles inline + variables CSS du
 * thème de l'hôte, i18n FR/EN lue depuis `<html lang>`). La brique ne peut PAS importer les modules
 * de l'hôte (Tailwind/shadcn/lucide/i18n) : le bundle n'externalise que React → tout est autonome ici.
 */
import { type CSSProperties, type ReactNode } from 'react'

/* ── i18n ─────────────────────────────────────────────────────────────────── */
export type Lang = 'fr' | 'en'
export function currentLang(): Lang {
  const l = (document.documentElement.lang || 'en').toLowerCase()
  return l.startsWith('fr') ? 'fr' : 'en'
}

const DICT: Record<Lang, Record<string, string>> = {
  fr: {
    title: 'Créateur de plugin de templating',
    subtitle: 'Génère un plugin de templating prêt à l’emploi, dans un module nouveau ou existant',
    // commun
    back: 'Précédent', next: 'Suivant', cancel: 'Annuler', restart: 'Recommencer',
    loading: 'Chargement…', saving: 'Enregistrement…', required: 'Obligatoire', optional: 'Facultatif',
    yes: 'Oui', no: 'Non', none: 'Aucun', close: 'Fermer', errors_title: 'Veuillez corriger les erreurs',
    check_fields: 'Veuillez vérifier les champs requis.', generate_failed: 'La génération a échoué.',
    readonly_notice: 'Vous n’avez pas le droit de modifier cet assistant : il est en lecture seule.',
    no_access: 'Vous n’avez pas les droits pour consulter cet outil.',
    blocking_title: 'Configuration du serveur incomplète',
    blocking_desc: 'L’outil ne peut pas générer de plugin tant que ces points ne sont pas corrigés :',
    // étape 1
    s1_name: 'Nom du plugin', s1_dest: 'Destination du plugin',
    s1_new: 'Nouveau module', s1_existing: 'Module de site existant',
    s1_new_name: 'Nom du nouveau module', s1_existing_name: 'Module de site',
    s1_no_site_modules: 'Aucun module de site n’est présent sur la plateforme : créez un nouveau module.',
    s1_choose: 'Choisissez…',
    // étape 2
    s2_desc: 'Titre et description affichés dans la liste des plugins, pour chaque langue. Au moins une langue doit être renseignée.',
    s2_title_f: 'Titre du plugin', s2_desc_f: 'Description',
    s2_thumb: 'Vignette du plugin', s2_thumb_hint: 'Formats : GIF, JPG, PNG — 190×100 recommandé, {max} maximum.',
    s2_thumb_pick: 'Choisir une image', s2_thumb_remove: 'Retirer la vignette',
    s2_thumb_missing: 'Une vignette est requise pour générer le plugin.',
    // étape 3
    s3_desc: 'Définissez les propriétés éditables du plugin. Le premier champ (template_path) est imposé.',
    s3_count: 'Nombre de propriétés', s3_count_hint: 'Entre 1 et {max}, template_path compris.',
    s3_field: 'Propriété {n}', s3_locked: 'Imposé',
    s3_f_name: 'Nom technique', s3_f_type: 'Type d’affichage', s3_f_required: 'Champ obligatoire',
    s3_f_options: 'Options de la liste', s3_f_options_hint: 'Séparez les options par une virgule.',
    s3_f_default: 'Valeur par défaut',
    // étape 4
    s4_desc: 'Libellé et infobulle de chaque propriété, par langue. Une langue complète suffit.',
    s4_label: 'Libellé', s4_tooltip: 'Infobulle', s4_option_label: 'Libellé de l’option « {v} »',
    // étape 5
    s5_desc: 'Vérifiez la configuration avant de générer le plugin.',
    s5_plugin: 'Plugin', s5_target: 'Module cible', s5_tpl: 'Chemin de template',
    s5_texts: 'Textes', s5_fields: 'Propriétés', s5_translations: 'Traductions',
    s5_no_access: 'Vous n’avez pas le droit de consulter le récapitulatif.',
    // étape 6
    s6_desc_new: 'Choisissez le site sur lequel activer le plugin, puis lancez la génération.',
    s6_desc_existing: 'Lancez la génération : le plugin sera écrit dans le module choisi.',
    s6_site: 'Site', s6_activate: 'Activer le plugin après création',
    s6_activate_note: 'L’activation nécessite un rechargement de la plateforme.',
    s6_generate: 'Terminer et créer le plugin', s6_generating: 'Génération en cours…',
    s6_no_access: 'Vous n’avez pas le droit de générer un plugin.',
    ok_title: 'Le plugin a été créé', ok_module: 'Module : {m}', ok_plugin: 'Plugin : {p}',
    ok_reload: 'La plateforme va se recharger dans {n}…', ok_manual: 'Rechargez la page pour activer le plugin.',
    ok_new: 'Créer un autre plugin',
    // La vue « Old » ouvre le tool legacy, dont renderToolAction() vide le conteneur de session
    // PARTAGÉ avec la brique : le brouillon en cours est perdu. On prévient avant de basculer.
    old_resets: 'L’ancienne interface réinitialise l’assistant : le brouillon en cours sera perdu. Continuer ?',
  },
  en: {
    title: 'Templating Plugin Creator',
    subtitle: 'Generates a ready-to-use templating plugin, in a new or existing module',
    back: 'Back', next: 'Next', cancel: 'Cancel', restart: 'Start over',
    loading: 'Loading…', saving: 'Saving…', required: 'Required', optional: 'Optional',
    yes: 'Yes', no: 'No', none: 'None', close: 'Close', errors_title: 'Please fix the errors below',
    check_fields: 'Please check the required fields.', generate_failed: 'Generation failed.',
    readonly_notice: 'You are not allowed to edit this wizard: it is read-only.',
    no_access: 'You do not have permission to view this tool.',
    blocking_title: 'Incomplete server configuration',
    blocking_desc: 'The tool cannot generate a plugin until these are fixed:',
    s1_name: 'Plugin name', s1_dest: 'Plugin destination',
    s1_new: 'New module', s1_existing: 'Existing site module',
    s1_new_name: 'New module name', s1_existing_name: 'Site module',
    s1_no_site_modules: 'No site module exists on this platform: create a new module.',
    s1_choose: 'Choose…',
    s2_desc: 'Title and description shown in the plugin list, per language. At least one language is required.',
    s2_title_f: 'Plugin title', s2_desc_f: 'Description',
    s2_thumb: 'Plugin thumbnail', s2_thumb_hint: 'Formats: GIF, JPG, PNG — 190×100 recommended, {max} max.',
    s2_thumb_pick: 'Choose an image', s2_thumb_remove: 'Remove thumbnail',
    s2_thumb_missing: 'A thumbnail is required to generate the plugin.',
    s3_desc: 'Define the plugin’s editable properties. The first field (template_path) is enforced.',
    s3_count: 'Number of properties', s3_count_hint: 'Between 1 and {max}, template_path included.',
    s3_field: 'Property {n}', s3_locked: 'Enforced',
    s3_f_name: 'Technical name', s3_f_type: 'Display type', s3_f_required: 'Required field',
    s3_f_options: 'Dropdown options', s3_f_options_hint: 'Separate options with a comma.',
    s3_f_default: 'Default value',
    s4_desc: 'Label and tooltip of each property, per language. One complete language is enough.',
    s4_label: 'Label', s4_tooltip: 'Tooltip', s4_option_label: 'Label for option “{v}”',
    s5_desc: 'Check the configuration before generating the plugin.',
    s5_plugin: 'Plugin', s5_target: 'Target module', s5_tpl: 'Template path',
    s5_texts: 'Texts', s5_fields: 'Properties', s5_translations: 'Translations',
    s5_no_access: 'You are not allowed to view the summary.',
    s6_desc_new: 'Pick the site to activate the plugin on, then run the generation.',
    s6_desc_existing: 'Run the generation: the plugin will be written into the chosen module.',
    s6_site: 'Site', s6_activate: 'Activate plugin after creation',
    s6_activate_note: 'Activating requires a platform reload.',
    s6_generate: 'Finish and create the plugin', s6_generating: 'Generating…',
    s6_no_access: 'You are not allowed to generate a plugin.',
    ok_title: 'The plugin has been created', ok_module: 'Module: {m}', ok_plugin: 'Plugin: {p}',
    ok_reload: 'The platform will reload in {n}…', ok_manual: 'Reload the page to activate the plugin.',
    ok_new: 'Create another plugin',
    old_resets: 'The old interface resets the wizard: your current draft will be lost. Continue?',
  },
}

export type TFn = (key: string, vars?: Record<string, string | number>) => string
export function useT(): TFn {
  const lang = currentLang()
  return (key, vars) => {
    let s = DICT[lang][key] ?? key
    if (vars) for (const [k, v] of Object.entries(vars)) s = s.replaceAll(`{${k}}`, String(v))
    return s
  }
}

/** Toast vers le chrome React de l'hôte (même canal que `buildToolPage`). */
export function notify(kind: 'ok' | 'ko', title: string, message: string) {
  window.postMessage({ __melisNotif: true, kind, title, message }, '*')
}

export function formatBytes(bytes: number): string {
  const units = ['B', 'kB', 'MB', 'GB']
  const i = bytes > 0 ? Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1) : 0
  return `${Math.round((bytes / 1024 ** i) * 100) / 100} ${units[i]}`
}

/* ── Styles ───────────────────────────────────────────────────────────────── */
export const card: CSSProperties = { border: '1px solid var(--color-border)', background: 'var(--color-card)', borderRadius: 12, boxShadow: '0 1px 2px rgba(0,0,0,.04)' }
export const inputCss: CSSProperties = { height: 40, width: '100%', boxSizing: 'border-box', borderRadius: 8, border: '1px solid var(--color-input,var(--color-border))', background: 'var(--color-card)', color: 'var(--color-foreground)', padding: '0 12px', fontSize: 14, outline: 'none' }
export const textareaCss: CSSProperties = { ...inputCss, height: 'auto', minHeight: 88, padding: '10px 12px', resize: 'vertical', fontFamily: 'inherit' }
export const btnPrimary: CSSProperties = { display: 'inline-flex', alignItems: 'center', gap: 6, height: 36, padding: '0 14px', borderRadius: 8, border: 0, background: 'var(--color-primary)', color: 'var(--color-primary-foreground,#fff)', fontSize: 14, fontWeight: 500, cursor: 'pointer' }
export const btnGhost: CSSProperties = { display: 'inline-flex', alignItems: 'center', gap: 6, height: 36, padding: '0 12px', borderRadius: 8, border: '1px solid var(--color-border)', background: 'var(--color-card)', color: 'var(--color-foreground)', fontSize: 14, cursor: 'pointer' }
export const iconBtn: CSSProperties = { display: 'inline-flex', alignItems: 'center', justifyContent: 'center', width: 28, height: 28, borderRadius: 6, border: 0, background: 'transparent', color: 'var(--color-muted-foreground)', cursor: 'pointer' }
export const label: CSSProperties = { display: 'block', fontSize: 13, fontWeight: 500, marginBottom: 4, color: 'var(--color-foreground)' }
export const hint: CSSProperties = { marginTop: 4, fontSize: 12, color: 'var(--color-muted-foreground)' }
export const th: CSSProperties = { textAlign: 'left', padding: '8px 14px', fontSize: 11, fontWeight: 600, textTransform: 'uppercase', letterSpacing: '.04em', color: 'var(--color-muted-foreground)', whiteSpace: 'nowrap' }
export const td: CSSProperties = { padding: '8px 14px', fontSize: 14, color: 'var(--color-foreground)', borderTop: '1px solid var(--color-border)', verticalAlign: 'top' }
export const disabled: CSSProperties = { opacity: 0.55, pointerEvents: 'none' }

/* ── Icônes ───────────────────────────────────────────────────────────────── */
const sIcon = { width: 15, height: 15, flexShrink: 0 } as const
export const CheckIcon = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5" /></svg>
export const LockIcon = () => <svg style={{ width: 12, height: 12 }} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></svg>
export const TrashIcon = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2m2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /></svg>
export const ArrowLeft = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 12H5M12 19l-7-7 7-7" /></svg>
export const ArrowRight = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
export const RotateIcon = () => <svg style={sIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 2v6h6" /><path d="M3 13a9 9 0 1 0 3-7.7L3 8" /></svg>
export const SpinIcon = () => (
  <svg style={{ ...sIcon, animation: 'melis-tpc-spin 1s linear infinite' }} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
    <path d="M21 12a9 9 0 1 1-6.2-8.6" />
  </svg>
)

/* ── Composants ───────────────────────────────────────────────────────────── */

export function Field({ label: lbl, required, hint: h, error, children }: {
  label: string; required?: boolean; hint?: string; error?: string[]; children: ReactNode
}) {
  return (
    <div>
      <label style={label}>
        {lbl}{required && <span style={{ color: 'var(--color-destructive,#ef4444)', marginLeft: 3 }}>*</span>}
      </label>
      {children}
      {h && <p style={hint}>{h}</p>}
      {error?.map((m, i) => (
        <p key={i} style={{ marginTop: 4, fontSize: 12, color: 'var(--color-destructive,#ef4444)' }}
           // Les messages legacy contiennent parfois du HTML (<b>chemin</b>) : ils viennent des
           // fichiers de langue du module, jamais d'une saisie utilisateur.
           dangerouslySetInnerHTML={{ __html: m }} />
      ))}
    </div>
  )
}

export function ErrorBanner({ title, messages }: { title: string; messages: string[] }) {
  if (!messages.length) return null
  return (
    <div style={{ ...card, borderColor: '#fca5a5', background: 'color-mix(in srgb, #ef4444 8%, var(--color-card))', padding: '10px 14px' }}>
      <p style={{ margin: 0, fontSize: 13, fontWeight: 600, color: '#b91c1c' }}>{title}</p>
      <ul style={{ margin: '6px 0 0', paddingLeft: 18, fontSize: 13, color: '#b91c1c' }}>
        {messages.map((m, i) => <li key={i} dangerouslySetInnerHTML={{ __html: m }} />)}
      </ul>
    </div>
  )
}

export function Notice({ children, tone = 'info' }: { children: ReactNode; tone?: 'info' | 'warn' }) {
  const color = tone === 'warn' ? '#b45309' : 'var(--color-muted-foreground)'
  const bg = tone === 'warn' ? 'color-mix(in srgb, #f59e0b 10%, var(--color-card))' : 'color-mix(in srgb, var(--color-muted,#888) 8%, transparent)'
  return <div style={{ ...card, background: bg, padding: '10px 14px', fontSize: 13, color }}>{children}</div>
}

export function Toggle({ on, onClick, disabled: off }: { on: boolean; onClick: () => void; disabled?: boolean }) {
  return (
    <button type="button" onClick={onClick} disabled={off} style={{
      width: 44, height: 24, borderRadius: 999, border: 0, cursor: off ? 'not-allowed' : 'pointer', padding: 2,
      opacity: off ? 0.5 : 1, background: on ? 'var(--color-primary)' : 'var(--color-border)', transition: 'background .15s',
    }}>
      <span style={{ display: 'block', width: 20, height: 20, borderRadius: '50%', background: '#fff', transform: on ? 'translateX(20px)' : 'translateX(0)', transition: 'transform .15s' }} />
    </button>
  )
}

/**
 * Drapeau de la langue. Images livrées par MelisCore (`public/images/lang/<locale>.png`, servies
 * par MelisAssetManager sous /MelisCore/) : les emojis drapeaux ne se rendent pas sous Windows.
 * Locale inconnue (pas de png) → l'image se masque, la pastille garde juste son libellé.
 */
function LangFlag({ locale, dim }: { locale: string; dim?: boolean }) {
  return (
    <img
      src={`/MelisCore/images/lang/${locale}.png`}
      alt=""
      width={16}
      height={11}
      style={{
        display: 'block', borderRadius: 2, objectFit: 'cover', flexShrink: 0,
        // Langue non sélectionnée : drapeau désaturé → la pastille active ressort au premier coup d'œil.
        filter: dim ? 'grayscale(1)' : 'none', opacity: dim ? 0.55 : 1, transition: 'filter .15s, opacity .15s',
      }}
      onError={(e) => { (e.currentTarget as HTMLImageElement).style.display = 'none' }}
    />
  )
}

/** Sélecteur de langue en pastilles (étapes 2 et 4). */
export function LangTabs({ langs, active, onChange, filled }: {
  langs: { locale: string; name: string }[]; active: string; onChange: (l: string) => void; filled?: (locale: string) => boolean
}) {
  return (
    <div style={{ display: 'inline-flex', gap: 4, padding: 4, borderRadius: 8, border: '1px solid var(--color-border)', background: 'color-mix(in srgb, var(--color-muted,#888) 12%, transparent)' }}>
      {langs.map((l) => {
        const on = l.locale === active
        return (
          <button key={l.locale} type="button" onClick={() => onChange(l.locale)} style={{
            display: 'inline-flex', alignItems: 'center', gap: 6, height: 28, padding: '0 12px', borderRadius: 6, border: 0,
            fontSize: 12, fontWeight: 500, cursor: 'pointer',
            background: on ? 'var(--color-card)' : 'transparent', color: on ? 'var(--color-foreground)' : 'var(--color-muted-foreground)',
            boxShadow: on ? '0 1px 2px rgba(0,0,0,.06)' : 'none',
          }}>
            <LangFlag locale={l.locale} dim={!on} />
            {filled?.(l.locale) && <span style={{ width: 6, height: 6, borderRadius: '50%', background: '#22c55e' }} />}
            {l.name}
          </button>
        )
      })}
    </div>
  )
}

/** Volet monté en permanence, montré/caché en CSS — ne remonte pas (ni refetch, ni perte de saisie). */
export function Pane({ show, children }: { show: boolean; children: ReactNode }) {
  return <div style={{ display: show ? 'block' : 'none' }}>{children}</div>
}

/** Keyframes du spinner : la brique ne dispose pas du CSS de l'hôte. */
export function BrickStyles() {
  return <style>{`@keyframes melis-tpc-spin{to{transform:rotate(360deg)}}`}</style>
}
