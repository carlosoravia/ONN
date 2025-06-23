<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
   <style>
        body {
            font-family: Figtree, ui-sans-serif, sans-serif;
            font-size: 12px;
            color: #111827;
            line-height: 1.5;
        }

        .title {
            background-color: #1e3a8a;
            color: white;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
        }

        .section {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .red {
            color: #dc2626;
            font-weight: bold;
        }

        .light {
            color: #4f6592;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            font-size: 11px;
            word-wrap: break-word;
        }

        th {
            background-color: #f3f4f6;
            color: #1e293b;
            font-weight: 600;
            font-size: 11px;
        }

        tr:nth-child(even) td {
            background-color: #f9fafb;
        }
    </style>

</head>
<body>

    <h4>IO 05 – TRACCIABILITÀ LOTTI COMPONENTI (PRE-ASSEMBLATI)</h4>

    <div class="title">PRE-ASSEMBLATI</div>

    <div class="section">
        <span class="red">CODICE PRE-ASSEMBLATO:</span>
        <span class="light">{{ $preAssembled->code }}</span><br>

        <span class="red">LOTTO N°:</span>
        <span class="light">{{ $lotto->code_lotto }}</span><br>

        <span class="red">N° PZ LOTTO:</span>
        <span class="light">{{ $lotto->quantity }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>ARTICOLO UTILIZZATO<br><small>(componenti necessari per l'assemblaggio)</small></th>
                <th>CODICE ARTICOLO<br><small>ONN WATER</small></th>
                <th>LOTTO ARTICOLO FORNITORE UTILIZZATO</th>
            </tr>
        </thead>
        <tbody style="border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; font-size: 11px; word-wrap: break-word">
            @foreach($articles as $a)
                <tr style="border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; font-size: 11px; word-wrap: break-word;">
                    <td style="border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top; font-size: 11px; word-wrap: break-word;">{{ $a->description }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top;font-size: 11px; word-wrap: break-word;">{{ $a->code }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px; text-align: left; vertical-align: top;font-size: 11px; word-wrap: break-word;">{{ $supplier_codes[$loop->index] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
