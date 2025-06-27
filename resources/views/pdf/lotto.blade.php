<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        .title {
            background-color: #1e3a8a;
            color: white;
            text-align: center;
            padding: 6px;
            font-weight: bold;
        }
        .section {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .red {
            color: #dc2626;
            font-weight: bold;
        }
        .light {
            color: #6b7280;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
        }
        th {
            background-color: #f3f4f6;
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
        <tbody>
            @foreach($articles as $a)
                <tr>
                    <td>{{ $a->description }}</td>
                    <td>{{ $a->code }}</td>
                    <td style="display: flex; flex-direction:row; align-items:center;">
                        <span>{{ $supplier_codes[$loop->index] ?? 'N/A' }}</span>
                        <span>
                            @if($a->is_moca)
                                <img src="./images/moca_article.png" style="height: 30px; margin: auto;" alt="">
                            @endif
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
