# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](https://keepachangelog.com/de/1.0.0/).

---

## [Unreleased]

### Fixed
- `config/services.yaml`: `../src/Widget/` aus dem Symfony-DI-Autowiring ausgeschlossen — Contao-Widgets dürfen nicht als Services registriert werden
- `TimestampWidget`: Fallback auf 8 Sekunden wenn `minTime` im Backend `0` oder leer ist (verhindert, dass alle Einreichungen durchkommen)
- `ContaoSimpleSpamTrapBundleC5`: Monolog-Handler umbenannt zu `solidwork_spam_trap_file` (eindeutiger Präfix verhindert Kollision mit App-Konfiguration)
- `AddSpamTrapCssListener`: Korrekte `generatePage`-Hook-Signatur mit `PageModel`, `LayoutModel`, `PageRegular`
- `contao/dca/tl_form_field.php`: `minTime`-Feld auf `mandatory => true` und SQL-Default `8` geändert (statt `0`)

### Added
- `LICENSE`-Datei (LGPL-3.0-or-later)
- `composer.json`: `keywords`, `homepage`, `support`-Block und `psr/log`-Abhängigkeit ergänzt
- Caching-Hinweis in `README.md`: Formularseiten müssen vom Page-Cache ausgeschlossen sein
- Dediziertes Spam-Logging über einen eigenen Monolog-Channel `spam_trap`
  - Logs werden täglich rotierend unter `var/log/spam-trap/spam-trap-YYYY-MM-DD.log` gespeichert
  - Aufbewahrungsdauer: 30 Tage
  - `HoneypotWidget`: loggt IP, URI und User-Agent bei befülltem Honeypot-Feld
  - `TimestampWidget`: loggt IP, URI, User-Agent, verstrichene Zeit und Mindestzeit bei zu schnellem Submit
  - Monolog-Konfiguration wird automatisch über `PrependExtensionInterface` eingespielt — keine manuelle App-Konfiguration nötig
- `README.md` mit vollständiger Dokumentation, Best Practices und Logging-Beschreibung

### Changed
- `ContaoSimpleSpamTrapBundleC5` implementiert nun `PrependExtensionInterface` zur automatischen Monolog-Konfiguration

---

## [1.0.0] — Initiale Version

### Added
- `HoneypotWidget`: verstecktes Textfeld zur Spam-Erkennung (Bots befüllen es, Menschen nicht)
  - CSS-basiertes Verstecken via `position: absolute; left: -9999px`
  - `tabindex="-1"` und `aria-hidden="true"` für Accessibility
  - Fehlermeldung übersetzbar über `TL_LANG['ERR']['honeypot']`
- `TimestampWidget`: zeitbasierter Spam-Schutz
  - Speichert Unix-Timestamp beim Laden des Formulars
  - Prüft Mindestzeit zwischen Laden und Absenden (Standard: 8 Sekunden)
  - Mindestzeit über Backend-Feld `minTime` konfigurierbar
  - Fehlermeldung übersetzbar über `TL_LANG['ERR']['timestamp']`
- Übersetzungen für **Deutsch** und **Englisch**
- CSS-Asset `public/css/spam-trap.css` zur visuellen Versteckung der Felder
- Automatische CSS-Einbindung über `generatePage`-Hook (`AddSpamTrapCssListener`)
- DCA-Erweiterung für `tl_form_field` mit Paletten und `minTime`-Feld
- Contao-Manager-Plugin für kompatible Bundle-Registrierung
- Unterstützung für Contao ^5.3 und PHP ^8.1