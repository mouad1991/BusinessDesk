{{--
    Lignes vides dynamiques pour remplir la page sans déborder.
    Estime le "poids" vertical du contenu (sauts de ligne + retour automatique)
    puis complète jusqu'à un budget de lignes, tout en évitant la 2e page.

    Variables attendues : $document, $cols (nombre de colonnes), $pdfTheme (optionnel)
--}}
@php
    $cols = $cols ?? 6;
    $lineBudget = ($pdfTheme ?? 'modern') === 'compact' ? 8 : 12;

    $usedLines = 0;
    foreach ($document->items as $it) {
        // Une catégorie occupe une ligne
        if (!empty($it->is_category)) { $usedLines += 1; continue; }

        // Convertir les sauts HTML en \n puis retirer les balises
        $plain = preg_replace('/<br\s*\/?>|<\/p>|<\/li>|<\/div>/i', "\n", $it->description ?? '');
        $plain = trim(strip_tags($plain));

        $explicitLines = substr_count($plain, "\n") + 1;                 // sauts de ligne explicites
        $flat = str_replace("\n", '', $plain);
        $wrapLines = max(1, (int) ceil(mb_strlen($flat) / 60));          // retour auto (~60 car / ligne)

        $usedLines += max($explicitLines, $wrapLines);
    }

    $emptyRows = max(0, $lineBudget - $usedLines);
@endphp
@for($i = 0; $i < $emptyRows; $i++)
<tr class="empty-row">@for($c = 0; $c < $cols; $c++)<td></td>@endfor</tr>
@endfor
