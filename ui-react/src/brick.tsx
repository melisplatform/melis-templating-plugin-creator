import TpcPage from './TpcPage'

/**
 * Point d'entrée de la brique. Quand ce bundle IIFE est chargé par le shell MelisCore, il
 * auto-enregistre son composant de page sous l'id de la brique. L'hôte le monte sur la route
 * dérivée de l'arbre de menu (via `forwardKey`), la route du manifeste servant de repli.
 */
declare global {
  interface Window {
    __melisRegisterBrick?: (b: { id: string; Component: unknown }) => void
  }
}

window.__melisRegisterBrick?.({ id: 'templating-plugin-creator', Component: TpcPage })
