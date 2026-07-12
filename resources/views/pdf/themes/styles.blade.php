@php
/*
 * PDF Theme Styles — included inside <style> by each document template.
 * Themes: modern | amber | rounded | colorful
 */
$validThemes = ['modern', 'amber', 'rounded', 'colorful'];
$t = in_array($pdfTheme ?? 'modern', $validThemes) ? $pdfTheme : 'modern';
@endphp
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #222; background: #fff; }
@page { margin: 15mm 12mm 22mm 12mm; }

.pdf-header { width: 100%; border-collapse: separate; margin-bottom: 8px; }
.header-col-logo { width: 22%; padding: 8px 10px; vertical-align: middle; text-align: center; }
.header-col-info  { width: 42%; padding: 8px 12px; vertical-align: middle; }
.header-col-doc   { width: 36%; padding: 10px 14px; vertical-align: middle; text-align: center; }
.company-logo { max-height: 65px; max-width: 150px; }
.company-name-text { font-size: 11pt; font-weight: bold; line-height: 1.2; }
.header-company-name { font-size: 9.5pt; font-weight: bold; margin-bottom: 3px; }
.header-line { font-size: 7.5pt; color: #444; line-height: 1.5; }
.doc-title  { font-size: 13pt; font-weight: bold; letter-spacing: 1px; line-height: 1.1; }
.doc-number { font-size: 8.5pt; margin-top: 4px; }
.doc-date   { font-size: 8pt; margin-top: 2px; }

.info-row { display: table; width: 100%; margin-top: 10px; }
.info-col { display: table-cell; width: 50%; vertical-align: top; padding: 8px 10px; }
.section-label { font-size: 8pt; font-weight: bold; padding-bottom: 2px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.info-line { font-size: 8.5pt; margin-bottom: 3px; }
.info-label { color: #555; }
.info-value { font-weight: bold; color: #222; }
.object-row { padding: 5px 10px; font-size: 8.5pt; margin-top: 4px; }
.object-label { font-weight: bold; }

.items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.items-table thead th { padding: 6px; font-size: 8.5pt; font-weight: bold; text-align: left; color: #fff; }
.items-table thead th.right  { text-align: right; }
.items-table thead th.center { text-align: center; }
.items-table tbody tr { border-bottom: 1px solid #e0e8f2; }
.items-table tbody td { padding: 5px 6px; font-size: 9pt; }
.items-table tbody td.center { text-align: center; }
.items-table tbody td.right  { text-align: right; }
.items-table thead th, .items-table tbody td.center, .items-table tbody td.right { white-space: nowrap; }
.items-table tbody td.bold   { font-weight: bold; }
.empty-row td { height: 16px; border-bottom: 1px solid #e8edf5; }

.totals-section { margin-top: 6px; }
.totals-table { float: right; width: 260px; }
.totals-table table { width: 100%; border-collapse: collapse; }
.totals-table table td { padding: 4px 8px; font-size: 9pt; }
.totals-table table td:last-child { text-align: right; font-weight: bold; }
.ttc-row td { font-size: 9.5pt !important; font-weight: bold; color: #fff !important; padding: 6px 10px !important; }
.clearfix { clear: both; }

.amount-words { margin-top: 10px; padding: 6px 10px; font-size: 8pt; }
.amount-words-label { color: #555; font-style: italic; }
.amount-words-value { font-weight: bold; color: #222; }
.amount-label { font-size: 7.5pt; color: #555; font-style: italic; }
.amount-value { font-size: 8pt; font-weight: bold; display: block; margin-top: 4px; }

.bottom-section { display: table; width: 100%; margin-top: 10px; }
.bottom-left  { display: table-cell; width: 50%; vertical-align: top; padding: 8px 10px; }
.bottom-right { display: table-cell; width: 50%; vertical-align: top; padding: 8px 10px; }
.bank-label { font-size: 8pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
.bank-line  { font-size: 8pt; margin-bottom: 3px; }

/* PDF — Totaux + Règlements côte à côte */
.totals-payments-wrap { width: 100%; border-collapse: collapse; margin-top: 8px; }
.totals-payments-left  { vertical-align: top; padding-right: 8px; }
.totals-payments-right { vertical-align: top; width: 265px; }

/* Tableau des totaux (colonne droite) */
.totals-inner-table { width: 100%; border-collapse: collapse; border: 1px solid #dce3ec; }
.totals-inner-table td { padding: 4px 8px; font-size: 9pt; }
.totals-inner-table td:last-child { text-align: right; font-weight: bold; border-left: 1px solid #dce3ec; }

/* PDF Règlements (colonne gauche) */
.pdf-payments { border: 1px solid #dce3ec; border-radius: 2px; overflow: hidden; }
.pdf-payments-header { display: table; width: 100%; background: #f8fafc; padding: 4px 8px; border-bottom: 1px solid #dce3ec; }
.pdf-payments-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #1a3a5c; display: table-cell; vertical-align: middle; }
.pdf-payment-badge { display: table-cell; text-align: right; vertical-align: middle; font-size: 7pt; font-weight: bold; color: #fff; padding: 2px 6px; border-radius: 8px; }
.pdf-payments-table { width: 100%; border-collapse: collapse; }
.pdf-payments-table thead th { background: #f0f5fb; padding: 3px 6px; font-size: 7.5pt; font-weight: bold; text-align: left; border-bottom: 1px solid #dce3ec; color: #334155; }
.pdf-payments-table thead th.right { text-align: right; }
.pdf-payments-table tbody td { padding: 3px 6px; font-size: 8pt; border-bottom: 1px solid #f0f5fb; }
.pdf-payments-table tbody tr:last-child td { border-bottom: none; }
.pdf-payments-footer { background: #f8fafc; padding: 4px 8px; font-size: 8pt; border-top: 1px solid #dce3ec; display: table; width: 100%; }
.pdf-payments-footer span { display: table-cell; }
.pdf-payments-footer span:last-child { text-align: right; }
.pdf-payments-remaining { color: #dc2626; }
.pdf-payments-paid { color: #16a34a; }

/* PDF Notes / Observations (colonne gauche) */
.pdf-notes { border: 1px solid #dce3ec; border-radius: 2px; overflow: hidden; }
.pdf-notes-title { background: #f8fafc; padding: 4px 8px; font-size: 8pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #1a3a5c; border-bottom: 1px solid #dce3ec; }
.pdf-notes-body { padding: 6px 8px; font-size: 8.5pt; color: #334155; line-height: 1.4; white-space: pre-wrap; }

/* PDF Category rows */
.pdf-category-row { }
.pdf-category-title { font-weight: bold; font-size: 9pt; padding: 5px 8px; text-align: center; color: #1a3a5c; border-bottom: 1px solid #e0e8f2; }
.items-table td ul, .items-table td ol { margin: 2px 0 2px 0; padding-left: 16px; }
.items-table td li { margin: 1px 0; }

.conditions { margin-top: 12px; padding: 8px 10px; }
.conditions-title { font-size: 8pt; font-weight: bold; margin-bottom: 5px; }
.conditions p { font-size: 7.5pt; color: #555; margin-bottom: 3px; font-style: italic; }

.signatures { display: table; width: 100%; margin-top: 10px; }
.sig-col { display: table-cell; width: 50%; text-align: center; padding: 8px; }
.sig-title { font-size: 8.5pt; font-weight: bold; margin-bottom: 30px; text-align: center; }
.sig-line { border-top: 1px solid #999; margin: 4px 20px; }
.sig-approved { font-size: 7.5pt; color: #555; font-style: italic; }

.sig-section { margin-top: 30px; width: 50%; padding: 10px; }
.sig-info { font-size: 7.5pt; color: #555; font-style: italic; text-align: center; margin-top: 3px; }

.footer { position: fixed; bottom: 8px; left: 0; right: 0; text-align: center; font-size: 7pt; padding: 5px 10px; }

@if($t === 'modern')
.header-col-logo { background: #fff; border-right: 3px solid #0c4a6e; }
.header-col-info  { background: #fff; }
.header-col-doc   { background: #0c4a6e; }
.company-name-text, .header-company-name { color: #0c4a6e; }
.doc-title  { color: #fff !important; }
.doc-number { color: #7ec8e3; }
.doc-date   { color: #ccc; }
.info-row { border: 1px solid #dce3ec; }
.info-col-left { border-right: 1px solid #dce3ec; }
.section-label { color: {{ $docAccentColor ?? '#0ea5e9' }}; border-bottom: 1px solid {{ $docAccentColor ?? '#0ea5e9' }}; }
.object-row { background: #f0f9ff; border: 1px solid #dce3ec; border-top: none; }
.object-label { color: {{ $docAccentColor ?? '#0ea5e9' }}; }
.items-table thead tr { background: #0c4a6e; }
.items-table tbody tr:nth-child(even) { background: #f0f9ff; }
.totals-table { border: 1px solid #dce3ec; }
.totals-table table td:last-child { border-left: 1px solid #dce3ec; }
.ttc-row { background: #0c4a6e; }
.amount-words { border: 1px solid #dce3ec; }
.bottom-section { border: 1px solid #dce3ec; }
.bottom-left { border-right: 1px solid #dce3ec; }
.bottom-right { background: #f0f9ff; }
.bank-label { color: {{ $docAccentColor ?? '#0ea5e9' }}; }
.conditions { border: 1px solid #dce3ec; background: #fafafa; }
.conditions-title { color: {{ $docAccentColor ?? '#0ea5e9' }}; }
.signatures { border: 1px solid {{ $sigAccentColor ?? ($docAccentColor ?? '#dce3ec') }}; }
.sig-col { border: 1px solid {{ $sigAccentColor ?? '#dce3ec' }}; }
.sig-col:first-child { border-right: none; }
.sig-title { color: {{ $sigAccentColor ?? ($docAccentColor ?? '#0ea5e9') }}; }
.sig-section { border: 1px solid #dce3ec; }
.footer { background: #0c4a6e; color: #fff; }
@endif

@if($t === 'amber')
.header-col-logo { background: #fff; }
.header-col-info  { background: #fff; text-align: center; }
.header-col-doc   { background: #e0f2fe; padding: 12px; }
.company-name-text { color: #1e293b; }
.header-company-name { color: #64748b; font-weight: normal; font-size: 8.5pt; }
.header-line { color: #94a3b8; }
.doc-title  { color: #1e293b !important; font-size: 11pt; }
.doc-number { color: #1e293b; font-weight: bold; font-size: 9pt; }
.doc-date   { color: #64748b; font-size: 8pt; }
.info-row { border: none; margin-top: 14px; }
.section-label { color: #1e293b; border-bottom: none; font-size: 8.5pt; }
.info-label { color: #94a3b8; }
.info-value { color: #f59e0b; font-weight: bold; }
.object-row { background: transparent; padding: 6px 0; font-style: italic; color: #64748b; }
.object-label { color: #1e293b; }
.items-table thead tr { background: #f59e0b; }
.items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
.totals-table { border: none; }
.totals-table table td { color: #475569; }
.totals-table table td:last-child { color: #1e293b; }
.ttc-row { background: #f59e0b; }
.amount-words { padding: 8px 0; color: #64748b; font-style: italic; }
.bottom-section { border: none; margin-top: 14px; }
.bottom-left { padding-left: 0; }
.bottom-right { background: #f8fafc; padding: 10px 12px; }
.bank-label { color: #f59e0b; }
.conditions { background: transparent; padding: 8px 0; border-top: 1px dashed #e2e8f0; }
.conditions-title { color: #f59e0b; }
.signatures { margin-top: 16px; }
.sig-col { border-top: 1px solid #e2e8f0; padding-top: 12px; }
.sig-title { color: #1e293b; }
.sig-section { border: 1px solid #f1f5f9; background: #fafafa; }
.footer { background: #fff; color: #94a3b8; border-top: 1px solid #e2e8f0; }
@endif

@if($t === 'rounded')
.header-col-logo { background: #fff; }
.header-col-info  { background: #fff; text-align: right; }
.header-col-doc   { background: #ea580c; padding: 14px; }
.company-name-text { color: #1e293b; }
.header-company-name { color: #64748b; font-size: 8.5pt; }
.header-line { color: #94a3b8; }
.doc-title  { color: #fff !important; font-size: 12pt; }
.doc-number { color: #fff; font-weight: bold; }
.doc-date   { color: #fed7aa; }
.info-row { border: none; margin-top: 14px; }
.info-col-left .info-line { color: #ea580c !important; }
.info-col-left .info-label { color: #ea580c !important; }
.info-col-left .info-value { color: #ea580c !important; }
.section-label { color: #ea580c; border-bottom: none; font-weight: bold; }
.info-line { margin-bottom: 4px; }
.info-value { font-weight: bold; }
.object-row { background: #fff7ed; padding: 8px 12px; color: #9a3412; }
.object-label { color: #ea580c; }
.items-table thead tr { background: #1e293b; }
.items-table tbody tr { border-bottom: 1px solid #f1f5f9; }
.items-table tbody tr:nth-child(even) { background: #f8fafc; }
.totals-table { border: 1px solid #e2e8f0; background: #fff; }
.totals-table table td { color: #475569; }
.totals-table table td:last-child { color: #1e293b; }
.ttc-row { background: #1e293b; }
.amount-words { border: 1px dashed #fed7aa; background: #fff7ed; padding: 8px 12px; color: #9a3412; }
.amount-words-value { color: #ea580c; }
.bottom-section { border: 1px solid #e2e8f0; }
.bottom-left { border-right: 1px solid #e2e8f0; }
.bottom-right { background: #fff7ed; }
.bank-label { color: #ea580c; }
.conditions { border: 1px solid #fed7aa; background: #fffbf5; }
.conditions-title { color: #ea580c; }
.signatures { border: 1px solid #e2e8f0; }
.sig-col { border: 1px solid #e2e8f0; }
.sig-col:first-child { border-right: none; }
.sig-title { color: #1e293b; }
.sig-section { border: 1px solid #fed7aa; background: #fffbf5; }
.footer { background: #1e293b; color: #fff; }
@endif

@if($t === 'colorful')
{{--
  COLORFUL — fond d'en-tête CYAN VIF (#0ea5e9), clairement différent de modern (navy).
  Bande arc-en-ciel en pied de page (élément HTML conditionnel dans footer.blade.php).
--}}
.header-col-logo { background: #fff; }
.header-col-info  { background: #f0f9ff; padding: 8px 14px; }
.header-col-doc   { background: #0ea5e9; padding: 10px 14px; }
.company-name-text { color: #0c4a6e; }
.header-company-name { color: #0c4a6e; font-size: 9pt; }
.header-line { color: #0369a1; }
.doc-title  { color: #fff !important; font-size: 13pt; letter-spacing: 1.5px; }
.doc-number { color: #e0f2fe; font-weight: bold; }
.doc-date   { color: #bae6fd; }

.info-row { border: none; margin-top: 12px; }
.info-col-left { border-right: 2px solid #0ea5e9; }
.section-label { color: #0ea5e9; border-bottom: 2px solid #0ea5e9; letter-spacing: 1px; }
.info-label { color: #64748b; }
.info-value { color: #0c4a6e; font-weight: bold; }
.object-row { background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 6px 12px; }
.object-label { color: #0ea5e9; }

.items-table thead tr { background: #0ea5e9; }
.items-table thead th { color: #fff; }
.items-table tbody tr { border-bottom: 1px solid #e0f2fe; }
.items-table tbody tr:nth-child(even) { background: #f0f9ff; }

.totals-table { border: 1px solid #bae6fd; }
.totals-table table td { color: #334155; }
.totals-table table td:last-child { border-left: 1px solid #bae6fd; color: #0c4a6e; }
.ttc-row { background: #0ea5e9; }

.amount-words { border: 1px solid #bae6fd; background: #f0f9ff; }
.amount-words-value { color: #0c4a6e; }
.bottom-section { border: 1px solid #bae6fd; }
.bottom-left { border-right: 1px solid #bae6fd; }
.bottom-right { background: #f0f9ff; }
.bank-label { color: #0ea5e9; }

.conditions { border: 1px solid #bae6fd; background: #f0f9ff; }
.conditions-title { color: #0ea5e9; }

.signatures { border: 1px solid #bae6fd; }
.sig-col { border: 1px solid #bae6fd; }
.sig-col:first-child { border-right: none; }
.sig-title { color: #0ea5e9; }
.sig-section { border: 1px solid #bae6fd; background: #f0f9ff; }

.footer { background: #fff; color: #64748b; font-size: 7pt; bottom: 14px; border-top: 1px solid #e2e8f0; }

.rainbow-stripe { display: table; position: fixed; bottom: 0; left: 0; right: 0; width: 100%; border-collapse: collapse; }
.rainbow-stripe td { height: 7px; padding: 0; border: none; }
@endif
