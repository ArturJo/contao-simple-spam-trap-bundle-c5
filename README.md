# Contao Simple Spam Trap Bundle (C5)

Einfacher Spam-Schutz für Contao-Formulare über zwei zusätzliche Formularfeld-Typen: **Honeypot** und **Zeitstempel**. Das Bundle ist speziell für Contao 5.3+ entwickelt und folgt den aktuellen Contao-5-Konventionen.

## Voraussetzungen

| Abhängigkeit | Version |
|---|---|
| PHP | ^8.1 |
| Contao | ^5.3 |

## Installation

```bash
composer require solidwork/contao-simple-spam-trap-bundle-c5
```

Anschließend die Datenbank aktualisieren (z. B. über den Contao-Manager oder die Konsole):

```bash
php bin/console contao:migrate
php bin/console assets:install
```

> Nach `assets:install` sind die CSS-Assets unter `bundles/contaosimplespamtrapc5/css/spam-trap.css` verfügbar.

> **Wichtig bei Page-Caching:** Das Timestamp-Feld schreibt den aktuellen Zeitstempel beim Seitenaufruf ins HTML. Wenn Contao Full-Page-Caching aktiv ist, wird ein veralteter Timestamp ausgeliefert — legitime Formulareinsendungen werden dann fälschlicherweise als Spam blockiert. Formularseiten sollten daher vom Cache ausgeschlossen sein (in Contao über das Seitenlayout konfigurierbar).

## Funktionsweise

Das Bundle registriert zwei neue Formularfeld-Typen im Contao-Formulargenerator. Beide Felder sind für den Besucher unsichtbar und erzeugen keinen zusätzlichen Aufwand für menschliche Nutzer.

### Honeypot-Spam-Schutz

Ein verstecktes Textfeld, das für Menschen unsichtbar ist, von automatisierten Bots aber typischerweise befüllt wird. Bei Formularübermittlung wird geprüft, ob das Feld leer geblieben ist — ist das nicht der Fall, wird die Übermittlung als Spam gewertet.

**Technische Details:**
- Rendert ein `<input type="text">` mit `tabindex="-1"` und `aria-hidden="true"`
- Per CSS aus dem sichtbaren Bereich verschoben (`position: absolute; left: -9999px`)
- Kein JavaScript notwendig
- Fehlermeldung bei Befüllung: übersetzbar über `TL_LANG['ERR']['honeypot']`

### Zeitbasierter Spam-Schutz (Timestamp)

Beim Laden des Formulars wird ein verstecktes Feld mit dem aktuellen Unix-Timestamp befüllt. Bei der Übermittlung wird geprüft, ob seit dem Laden mindestens eine konfigurierbare Mindestzeit vergangen ist. Bots, die Formulare sofort absenden, werden so erkannt.

**Technische Details:**
- Rendert ein `<input type="hidden">` mit dem aktuellen `time()`-Wert
- Standardmäßige Mindestzeit: **8 Sekunden**
- Konfigurierbar über das Backend-Feld „Mindestzeit in Sekunden"
- Fehlermeldung bei Unterschreitung: übersetzbar über `TL_LANG['ERR']['timestamp']`

## Einrichtung im Backend

1. Im Contao-Backend ein Formular öffnen oder neu anlegen.
2. Im Bereich **Formularfelder** ein neues Feld hinzufügen.
3. Als Typ **„Honeypot-Spam-Schutz"** oder **„Zeitbasierter Spam-Schutz"** wählen.
4. Einen internen Namen vergeben (z. B. `hp` oder `ts`).
5. Beim Zeitstempel-Feld optional die **Mindestzeit in Sekunden** anpassen (Standard: 8).
6. Speichern — fertig.

> **Empfehlung:** Beide Felder kombiniert einsetzen, um die Erkennungsrate zu erhöhen.

## Best Practices

### Feldplatzierung

- Das Honeypot-Feld sollte nicht das erste oder letzte Feld im Formular sein, damit es natürlicher wirkt.
- Das Timestamp-Feld kann an beliebiger Stelle stehen, da es ohnehin unsichtbar ist.

### Mindestzeit (Timestamp)

- Standardwert von **8 Sekunden** ist ein guter Ausgangspunkt für einfache Formulare.
- Kurze Formulare (nur E-Mail-Feld): 5–8 Sekunden sind ausreichend.
- Lange Formulare (viele Felder): 10–15 Sekunden können sinnvoll sein.
- Zu hohe Werte frustrieren echte Nutzer, die z. B. Autofill verwenden.

### Kombination beider Felder

```
[Vorname]
[Nachname]
[E-Mail]
[Honeypot]     ← unsichtbar für Menschen
[Timestamp]    ← unsichtbar für Menschen
[Nachricht]
[Absenden]
```

Beide Felder ergänzen sich: Der Honeypot erkennt Bots, die alle Felder befüllen; der Timestamp erkennt Bots, die Formulare sofort absenden.

### Kein JavaScript erforderlich

Der Spam-Schutz funktioniert vollständig serverseitig und CSS-basiert — kein JavaScript notwendig. Das macht ihn robust gegenüber Browsern mit deaktiviertem JavaScript.

### Accessibility

- Das Honeypot-Feld trägt `aria-hidden="true"` und `tabindex="-1"`, sodass es von Screenreadern und Tastaturnavigation ignoriert wird.
- Das Timestamp-Feld ist ein `type="hidden"` und damit für alle Nutzer transparent.

## CSS-Anpassung

Das Bundle liefert ein minimales CSS-Stylesheet, das über den `generatePage`-Hook automatisch eingebunden wird. Es kann bei Bedarf in eigenen Projekten überschrieben werden:

```css
/* Honeypot aus dem sichtbaren Bereich verschieben */
.widget-honeypot,
.hp-field {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
}

/* Timestamp-Wrapper verstecken */
.widget-timestamp {
    display: none;
}
```

## Übersetzungen

Das Bundle enthält Übersetzungen für **Deutsch** und **Englisch**. Eigene Fehlermeldungen können über die Standard-Contao-Sprach-Override-Mechanismen angepasst werden:

```php
// contao/languages/de/default.php (im eigenen Projekt)
$GLOBALS['TL_LANG']['ERR']['honeypot'] = 'Eigene Fehlermeldung.';
$GLOBALS['TL_LANG']['ERR']['timestamp'] = 'Eigene Fehlermeldung.';
```

## Logging

Das Bundle schreibt alle erkannten Spam-Versuche automatisch in ein dediziertes Log-Verzeichnis:

```
var/
└── log/
    └── spam-trap/
        ├── spam-trap-2026-03-01.log
        ├── spam-trap-2026-03-02.log
        └── spam-trap-2026-03-03.log
```

Die Logs rotieren täglich. Es werden maximal **30 Tage** aufbewahrt, ältere Dateien werden automatisch gelöscht.

Jeder Eintrag enthält:

| Feld | Beschreibung |
|---|---|
| `field` | Interner Feldname im Formular |
| `ip` | IP-Adresse des Absenders |
| `uri` | Aufgerufene URL |
| `user_agent` | Browser / Bot-Kennung |
| `elapsed_sec` | Verstrichene Zeit seit Formular-Load *(nur Timestamp)* |
| `min_sec` | Konfigurierte Mindestzeit *(nur Timestamp)* |

**Beispiel-Eintrag (Honeypot):**
```
[2026-03-03 14:22:07] spam_trap.WARNING: Honeypot field filled — spam submission blocked
  {"field":"hp","ip":"1.2.3.4","uri":"/kontakt","user_agent":"curl/7.88"} []
```

**Beispiel-Eintrag (Timestamp):**
```
[2026-03-03 14:22:31] spam_trap.WARNING: Form submitted too fast — spam submission blocked
  {"field":"ts","elapsed_sec":1,"min_sec":8,"ip":"1.2.3.4","uri":"/kontakt","user_agent":"curl/7.88"} []
```

> Die Logs werden über einen eigenen Monolog-Channel `spam_trap` geschrieben, der vom Bundle automatisch konfiguriert wird — keine manuelle Anpassung der App-Konfiguration nötig.

## Projektstruktur

```
contao-simple-spam-trap-bundle-c5/
├── composer.json
├── config/
│   └── services.yaml                      # Symfony-Service-Autowiring
├── contao/
│   ├── config/config.php                  # Registriert TL_FFL-Typen
│   ├── dca/tl_form_field.php              # DCA-Paletten + minTime-Feld
│   ├── languages/
│   │   ├── de/default.php                 # Deutsche Fehlermeldungen
│   │   ├── de/tl_form_field.php           # Deutsche Backend-Labels
│   │   ├── en/default.php                 # Englische Fehlermeldungen
│   │   └── en/tl_form_field.php           # Englische Backend-Labels
│   └── templates/
│       ├── form_honeypot.html5            # Honeypot-Template
│       └── form_timestamp.html5           # Timestamp-Template
├── public/css/
│   └── spam-trap.css                      # Frontend-CSS
└── src/
    ├── ContaoSimpleSpamTrapBundleC5.php   # Bundle-Klasse
    ├── ContaoManager/Plugin.php           # Contao-Manager-Plugin
    ├── EventListener/
    │   └── AddSpamTrapCssListener.php     # generatePage-Hook für CSS
    └── Widget/
        ├── HoneypotWidget.php
        └── TimestampWidget.php
```

## Verwandtes Bundle

Für **Contao 4.13** gibt es die Vorgängerversion:
[solidwork/contao-simple-spam-trap-bundle](https://github.com/ArturJo/contao-simple-spam-trap-bundle)

## Lizenz

LGPL-3.0-or-later — siehe [LICENSE](https://www.gnu.org/licenses/lgpl-3.0.html)
