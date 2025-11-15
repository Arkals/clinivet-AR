<?php
// Autoload composer only if available
if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

use Dompdf\Dompdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

if (!function_exists('generarFacturaPDF')) {
function generarFacturaPDF($emisor, $receptor, $productos, $total, $uuid, $folio, $fecha, $metodo_pago, $forma_pago) {
    
    // Generar QR Code real con la información del CFDI según estándares del SAT
    $qrContent = "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?" .
                 "id=" . $uuid . 
                 "&re=" . $emisor['rfc'] . 
                 "&rr=" . $receptor['rfc'] . 
                 "&tt=" . str_pad(number_format($total, 6, '.', ''), 17, '0', STR_PAD_LEFT) .
                 "&fe=" . substr(str_replace(['-', ':', 'T', ' '], '', $fecha), 0, 8);
    
    $qrCode = '';
    try {
        if (class_exists('Endroid\QrCode\Builder\Builder')) {
            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $qrContent,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            );
            
            $result = $builder->build();
            $qrCode = 'data:image/png;base64,' . base64_encode($result->getString());
        }
    } catch (Exception $e) {
        // Fallback to simple QR representation if library fails
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 120 120"><rect width="120" height="120" fill="#fff" stroke="#222"/><rect x="6" y="6" width="32" height="32" fill="#222"/><rect x="82" y="6" width="32" height="32" fill="#222"/><rect x="6" y="82" width="32" height="32" fill="#222"/></svg>');
    }

    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
    @page { margin: 20mm; }
    body { 
        font-family: Arial, sans-serif; 
        margin: 0; 
        padding: 0; 
        font-size: 11px;
        line-height: 1.3;
        color: #000;
    }
    
    .container {
        width: 100%;
        max-width: 650px;
        margin: 0 auto;
    }
    
    .header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #4a47a3;
        padding-bottom: 10px;
    }
    
    .title {
        font-size: 24px;
        font-weight: bold;
        color: #4a47a3;
        margin: 0;
        text-transform: uppercase;
    }
    
    .subtitle {
        font-size: 14px;
        color: #666;
        margin: 5px 0 0 0;
    }
    
    .section {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        padding: 10px;
    }
    
    .section-title {
        background-color: #4a47a3;
        color: white;
        font-weight: bold;
        font-size: 12px;
        padding: 5px 8px;
        margin: -10px -10px 8px -10px;
        text-transform: uppercase;
    }
    
    .field {
        margin-bottom: 3px;
    }
    
    .field-label {
        font-weight: bold;
        display: inline-block;
        width: 120px;
    }
    
    .productos-table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 10px;
    }
    
    .productos-table th {
        background-color: #4a47a3;
        color: white;
        padding: 8px 5px;
        border: 1px solid #333;
        text-align: center;
        font-weight: bold;
    }
    
    .productos-table td {
        padding: 6px 5px;
        border: 1px solid #ddd;
        text-align: center;
    }
    
    .productos-table td:first-child {
        text-align: left;
    }
    
    .total-section {
        text-align: right;
        margin: 15px 0;
        font-size: 14px;
        font-weight: bold;
        color: #4a47a3;
    }
    
    .footer {
        margin-top: 20px;
        display: table;
        width: 100%;
    }
    
    .footer-left {
        display: table-cell;
        width: 65%;
        vertical-align: top;
        padding-right: 15px;
    }
    
    .footer-right {
        display: table-cell;
        width: 35%;
        text-align: center;
        vertical-align: top;
    }
    
    .qr-code {
        max-width: 120px;
        height: auto;
        border: 1px solid #ddd;
        padding: 5px;
    }
    
    .sello-digital {
        font-size: 9px;
        margin-top: 10px;
        line-height: 1.2;
    }
    
    .sello-title {
        font-weight: bold;
        color: #4a47a3;
        margin-bottom: 5px;
    }
    
    .sello-content {
        word-break: break-all;
        color: #666;
    }
    
    .certificados {
        margin-top: 10px;
        font-size: 9px;
    }
    </style></head><body>
    
    <div class="container">
        <div class="header">
            <h1 class="title">Factura Electrónica</h1>
            <p class="subtitle">CFDI Fiscal Digital</p>
        </div>
        
        <div class="section">
            <div class="section-title">Emisor</div>
            <div class="field"><span class="field-label">Razón Social:</span> '.htmlspecialchars($emisor['razon_social']).'</div>
            <div class="field"><span class="field-label">RFC:</span> '.htmlspecialchars($emisor['rfc']).'</div>
            <div class="field"><span class="field-label">Régimen Fiscal:</span> '.$emisor['regimen'].' - General de Ley Personas Morales</div>
        </div>
        
        <div class="section">
            <div class="section-title">Receptor</div>
            <div class="field"><span class="field-label">Cliente:</span> '.htmlspecialchars($receptor['nombre']).'</div>
            <div class="field"><span class="field-label">RFC:</span> '.htmlspecialchars($receptor['rfc']).'</div>
            <div class="field"><span class="field-label">Uso CFDI:</span> '.$receptor['uso_cfdi'].' – Gastos en general</div>
        </div>
        
        <div class="section">
            <div class="section-title">Datos Fiscales</div>
            <div class="field"><span class="field-label">Serie:</span> A</div>
            <div class="field"><span class="field-label">Folio:</span> '.htmlspecialchars($folio).'</div>
            <div class="field"><span class="field-label">UUID:</span> '.htmlspecialchars($uuid).'</div>
            <div class="field"><span class="field-label">Fecha:</span> '.date('d/m/Y', strtotime($fecha)).'</div>
            <div class="field"><span class="field-label">Método de Pago:</span> '.htmlspecialchars($metodo_pago).'</div>
            <div class="field"><span class="field-label">Forma de Pago:</span> '.htmlspecialchars($forma_pago).'</div>
        </div>
        
        <table class="productos-table">
            <thead>
                <tr>
                    <th style="width: 50%">Producto</th>
                    <th style="width: 15%">Cantidad</th>
                    <th style="width: 20%">Precio unitario</th>
                    <th style="width: 15%">Importe</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($productos as $p) {
        $html .= '<tr>
                    <td>'.htmlspecialchars($p['name']).'</td>
                    <td>'.$p['qty'].'</td>
                    <td>$'.number_format($p['price'],2).'</td>
                    <td>$'.number_format($p['qty']*$p['price'],2).'</td>
                  </tr>';
    }
    
    $html .= '</tbody></table>
        
        <div class="total-section">
            Total: $'.number_format($total,2).'
        </div>
        
        <div class="footer">
            <div class="footer-left">
                <div class="sello-digital">
                    <div class="sello-title">Sello Digital del CFDI:</div>
                    <div class="sello-content">'.substr($uuid, 0, 20).'...'.substr($uuid, -20).'SELLOFAKESATGOV</div>
                </div>
                
                <div class="sello-digital">
                    <div class="sello-title">Sello del SAT:</div>
                    <div class="sello-content">Por validar|No. de Serie del Certificado del SAT|Fecha y hora de certificación</div>
                </div>
                
                <div class="certificados">
                    <div><strong>No. de Serie del Certificado del Emisor:</strong> 00001000000403258748</div>
                    <div><strong>RFC del Proveedor de Certificación:</strong> AAA010101AAA</div>
                </div>
            </div>
            
            <div class="footer-right">
                <img src="'.$qrCode.'" alt="Código QR" class="qr-code" />
                <div style="font-size: 9px; margin-top: 5px; color: #666;">
                    Código QR para validación en<br>sat.gob.mx
                </div>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding: 10px; background-color: #f5f5f5; border: 1px solid #ddd; font-size: 9px; text-align: center; color: #666;">
            Esta es una representación impresa de un CFDI. Para consultar el detalle del CFDI, visite la página del SAT.
        </div>
    </div>
    
    </body></html>';

    if (!class_exists('\Dompdf\\Dompdf')) {
        // Dompdf no disponible: devolver una representación simple en texto para no bloquear el flujo
        return "%PDF-1.4\n% Dompdf no instalado - factura de texto:\n" . strip_tags($html);
    }
    
    $dompdfClass = '\\Dompdf\\Dompdf';
    $dompdf = new $dompdfClass();
    $dompdf->getOptions()->set('isRemoteEnabled', true);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return $dompdf->output();
}
}
