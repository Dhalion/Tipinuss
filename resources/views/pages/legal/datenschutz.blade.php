<x-legal-layout pageTitle="{{ __('app.legal.datenschutz') }}">
    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§1 Verantwortlicher</h2>
    <p class="mt-3">
        Verantwortlicher im Sinne der DSGVO ist die <strong>Waschnuss-Manufaktur GmbH & Co. KG</strong>,
        Sitz: Internet, überall und nirgends. Postadresse: <em>In der Cloud, links abbiegen bei der zweiten Firewall.</em>
    </p>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§2 Umfang der Datenverarbeitung</h2>
    <p class="mt-3">
        Die Waschnuss-Manufaktur verarbeitet folgende personenbezogene Daten ausschließlich zum Betrieb dieser
        privaten Wettplattform:
    </p>
    <ul class="mt-3 list-disc list-inside space-y-1">
        <li><strong>Bestandsdaten:</strong> Name, E-Mail-Adresse, Passwort (gehasht)</li>
        <li><strong>Nutzungsdaten:</strong> Wettverhalten, Transaktionshistorie (Waschnüsse), erstellte Wetten</li>
        <li><strong>Verbindungsdaten:</strong> IP-Adresse, User-Agent, Session-ID (120 Minuten gültig)</li>
    </ul>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§3 Zweck und Rechtsgrundlage</h2>
    <p class="mt-3">
        Die Datenverarbeitung erfolgt zum Zweck des Betriebs der privaten Wettplattform
        Tipinuss für einen geschlossenen Nutzerkreis. Rechtsgrundlage ist Art.&nbsp;6 Abs.&nbsp;1
        lit.&nbsp;f DSGVO (berechtigtes Interesse). Das berechtigte Interesse der Waschnuss-Manufaktur
        ist der Betrieb der Plattform für die registrierten Nutzer sowie der Schutz vor Missbrauch.
    </p>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§4 Cloudflare</h2>
    <p class="mt-3">
        Für die Authentifizierung und Content-Auslieferung setzt die Waschnuss-Manufaktur
        <a href="https://www.cloudflare.com" target="_blank" rel="noopener">Cloudflare, Inc.</a>
        (101 Townsend St, San Francisco, CA 94107, USA) als Auftragsverarbeiter ein.
        Cloudflare ist nach dem EU-US Data Privacy Framework (DPF) zertifiziert.
        Die Authentifizierung erfolgt per E-Mail-OTP; hierfür wird deine E-Mail-Adresse
        an Cloudflare übermittelt.
    </p>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§5 Schriftarten (fonts.bunny.net)</h2>
    <p class="mt-3">
        Diese Seite lädt Schriftarten von <a href="https://fonts.bunny.net" target="_blank" rel="noopener">fonts.bunny.net</a>
        (einem datenschutzfreundlichen Google-Fonts-Proxy). Beim Aufruf wird deine IP-Adresse
        an den Server von fonts.bunny.net übermittelt. Es werden keine Cookies gesetzt.
    </p>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§6 Speicherdauer</h2>
    <p class="mt-3">
        Personenbezogene Daten werden gelöscht, sobald der Zweck der Verarbeitung entfällt:
    </p>
    <ul class="mt-3 list-disc list-inside space-y-1">
        <li>Kontodaten: bis zur Löschung des Benutzerkontos</li>
        <li>Session-Daten: 120 Minuten nach Ende der Sitzung</li>
        <li>Wettdaten: werden für den Spielbetrieb benötigt und nach Kontolöschung anonymisiert</li>
    </ul>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§7 Betroffenenrechte</h2>
    <p class="mt-3">Du hast jederzeit das Recht auf:</p>
    <ul class="mt-3 list-disc list-inside space-y-1">
        <li>Auskunft über gespeicherte Daten (Art.&nbsp;15 DSGVO)</li>
        <li>Berichtigung unrichtiger Daten (Art.&nbsp;16 DSGVO)</li>
        <li>Löschung deiner Daten (Art.&nbsp;17 DSGVO)</li>
        <li>Einschränkung der Verarbeitung (Art.&nbsp;18 DSGVO)</li>
        <li>Datenübertragbarkeit (Art.&nbsp;20 DSGVO)</li>
        <li>Widerspruch gegen die Verarbeitung (Art.&nbsp;21 DSGVO)</li>
    </ul>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§8 Drittlandübermittlung</h2>
    <p class="mt-3">
        Eine Übermittlung in Drittländer findet nur statt, soweit dies für den Betrieb
        erforderlich ist (Cloudflare, Inc. – USA). Cloudflare ist unter dem EU-US DPF
        zertifiziert, sodass ein angemessenes Datenschutzniveau gewährleistet ist.
    </p>

    <h2 class="mt-10 text-lg font-bold text-zinc-900 dark:text-white">§9 Änderungen</h2>
    <p class="mt-3">
        Die Waschnuss-Manufaktur behält sich vor, diese Datenschutzerklärung anzupassen, um sie stets den
        rechtlichen Anforderungen anzupassen. Die jeweils aktuelle Version ist unter
        <a href="{{ route('legal.datenschutz') }}">{{ route('legal.datenschutz') }}</a> abrufbar.
    </p>
</x-legal-layout>