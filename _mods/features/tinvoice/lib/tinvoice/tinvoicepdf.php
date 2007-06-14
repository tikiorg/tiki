<?php

//define('FPDF_FONTPATH', '/var/www/tikiwiki/lib/ufpdf/font/');
//require_once("lib/ufpdf/ufpdf.php");
require_once("lib/fpdf/fpdf.php");

$GFONT="FreeSans";
//$GFONT="Times";
//$GFONT="Helvetica";

class Tinvoicepdf {
    /*private*/ var $invoice;
    
    /*public*/ function Tinvoicepdf($invoice) {
	$this->invoice=$invoice;
    }

    /*private*/ function pdf_add_left_line(&$hl, $pdf, $title, $value) {
	global $GFONT;

	$pdf->SetXY(20, $hl);
	$pdf->Cell(30,10, $title);
	$pdf->SetX(60);
	$pdf->Cell(60,10, "$value");
	$hl+=5;
    }

    /*private*/ function pdf_add_table_header(&$hl, $pdf) {
	global $GFONT;
	$pdf->SetFillColor(220);
	$pdf->Rect(20.5, $hl, 24.5, 4, "F");
	$pdf->Rect(45.5, $hl, 84.5, 4, "F");
	$pdf->Rect(130.5, $hl, 9.5, 4, "F");
	$pdf->Rect(140.5, $hl, 24.5, 4, "F");
	$pdf->Rect(165.5, $hl, 24.5, 4, "F");
	$pdf->SetFont($GFONT, "B", 10);
	$pdf->SetXY(20, $hl);
	$pdf->Cell(25, 5, "Réf", 0, 0, "C");
	$pdf->SetXY(45, $hl);
	$pdf->Cell(85, 5, "Désignation", 0, 0, "C");
	$pdf->SetXY(130, $hl);
	$pdf->Cell(10, 5, "Qté", 0, 0, "C");
	$pdf->SetXY(140, $hl);
	$pdf->Cell(25, 5, "P.U H.T", 0, 0, "C");
	$pdf->SetXY(165, $hl);
	$pdf->Cell(25, 5, "P.T H.T", 0, 0, "C");	
	$hl+=8;
    }

    /*private*/ function pdf_add_rib(&$hl, $pdf) {
	global $GFONT;
	$rib=$this->invoice->get_emitter_rib();
	if ($rib === NULL) return;

	$pdf->SetFillColor(220);
	$pdf->SetFont($GFONT, "", 9);

	$pdf->Rect(20.5, $hl, 19.5, 4, "F");
	$pdf->Rect(40.5, $hl, 149.5, 4, "F");
	$pdf->SetXY(20, $hl);
	$pdf->Cell(20, 5, "RIB:", 0, 0, "C");
	$pdf->SetXY(40, $hl);
	$pdf->Cell(150, 5,
		   $rib['domiciliation'].' - '.
		   $rib['code_banque'].' '.
		   $rib['code_guichet'].' '.
		   $rib['numero_compte'].' '.
		   $rib['cle_rib'],
		   0, 0, "C");
	$hl+=5;

	if (isset($rib['iban']) && strlen($rib['iban'])) {
	    $pdf->Rect(20.5, $hl, 19.5, 4, "F");
	    $pdf->Rect(40.5, $hl, 69.5, 4, "F");
	    $pdf->Rect(110.5, $hl, 19.5, 4, "F");
	    $pdf->Rect(130.5, $hl, 59.5, 4, "F");
	    $pdf->SetXY(20, $hl);
	    $pdf->Cell(20, 5, "IBAN:", 0, 0, "C");
	    $pdf->SetXY(40, $hl);
	    $pdf->Cell(70, 5, $rib['iban'], 0, 0, "C");
	    $pdf->SetXY(110, $hl);
	    $pdf->Cell(20, 5, "BIC:", 0, 0, "C");
	    $pdf->SetXY(130, $hl);
	    $pdf->Cell(60, 5, $rib['bic'], 0, 0, "C");
	    $hl+=5;
	}
    }

    /*private*/ function pdf_add_footer($pdf) {
	global $GFONT;
	$footer=$this->invoice->get_footer();
	if ($footer !== NULL) {
	    $pdf->SetXY(10, 287 - 8);
	    $pdf->SetFont($GFONT, "", 11);
	    $pdf->MultiCell(190, 4, $footer, 0, "C");
	}
    }

    /*public*/ function make_pdf() {
	global $GFONT;
  
	$paf=setlocale(LC_ALL, "fr_FR@euro");
	//if ($paf === FALSE) die("pas de locale\n");

	// init the pdf document
	$pdf=new FPDF('P', "mm", "A4");
	$pdf->SetAutoPageBreak(false);
	$pdf->AliasNbPages();
     	$pdf->AddFont($GFONT,'','FreeSans.php');
     	$pdf->AddFont($GFONT,'B','FreeSansBold.php');
//    	$pdf->AddFont($GFONT,'','DejaVuSans.php');
//    	$pdf->AddFont($GFONT,'B','DejaVuSans-Bold.php');
//     	$pdf->AddFont($GFONT,'','Vera.php');
//     	$pdf->AddFont($GFONT,'B','VeraBd.php');
// 	$pdf->AddFont($GFONT,'','bitstream-Vera.php');
// 	$pdf->AddFont($GFONT,'B','bitstream-VeraBold.php');
//   	$pdf->AddFont($GFONT,'','times.pdf');
//  	$pdf->AddFont($GFONT,'B','timesb.pdf');
	
	$pdf->AddPage();
	$this->pdf_add_footer($pdf);

	$hl=40;

	// add the header
	$image=$this->invoice->get_image();
	if ($image !== NULL) {
	    $pdf->Image($image['url'], $image['x'], $image['y'], $image['w'], $image['h']);
	    //$hl+=$image['h'];
	}

	// add the emitter
	$emitter_address=$this->invoice->get_emitter_address();
	if ($emitter_address !== NULL) {
	    $pdf->SetXY(20, $hl);
	    $pdf->SetFont($GFONT, "", 10);
	    $pdf->MultiCell(100, 4.5, $emitter_address);
	    $hl=$pdf->GetY();
	}

	// add the receiver address
	$receiver_address=$this->invoice->get_receiver_address();
	if ($receiver_address !== NULL) {
	    $pdf->SetXY(130, 50);
	    $pdf->SetFont($GFONT, "B", 10);
	    $pdf->Cell(100, 10, "Adresse Client :");
	    $pdf->SetXY(130, 60);
	    $pdf->SetFont($GFONT, "", 10);
	    $pdf->MultiCell(100, 4.5, $receiver_address);
	}

	// add the title "invoice"
	$pdf->SetXY(0, 90);
	$pdf->SetFont($GFONT, "B", 16);
	$pdf->Cell(210, 10, "Facture en Euros", 0, 0, "C");

	/*** left bloc ***/
	$pdf->SetFont($GFONT, "B", 10);
	$keys=array('libelle' => 'Libellé',
		    'ref' => 'Facture numéro',
		    'date' => 'Date de facturation',
		    'refdevis' => 'Réf devis',
		    'refbondecommande' => 'Réf bon de commande',
		    'receiver_tvanumber' => 'N° TVA Client');
	foreach($keys as $key => $title) {
	    $func="get_$key";
	    $value=$this->invoice->$func();
	    if (($value !== NULL) && ($value !== ''))
		$this->pdf_add_left_line(&$hl, $pdf, $title, $value);
	}
	for($i=0; $i<4; $i++) {
	    $value=$this->invoice->get_ref('custom'.$i);
	    if ($value !== NULL) {
		$value=explode("\x01", $value);
		$this->pdf_add_left_line(&$hl, $pdf, $value[0], $value[1]);
	    }
	}

	// add the table header
	$hl=110;
	$this->pdf_add_table_header(&$hl, $pdf);

	// add the table content
	$tht=0;
	$page=1;
	$pdf->SetFont($GFONT, "", 10);
	foreach($this->invoice->get_lines() as $v) {

	    if ($hl > 230) { // make a new page
		$hl+=4;
		$pdf->SetFont($GFONT, "B", 10);
		$pdf->SetXY(10, $hl);
		$pdf->MultiCell(190, 5, "Suite page suivante", 0, "C");
		$pdf->AddPage();
		$this->pdf_add_footer($pdf);
		$page++;
		//sgf_footer(&$sf);
		$hl=20;
		$pdf->SetFont($GFONT, "B", 10);
		$pdf->SetXY(10, $hl);
		$pdf->MultiCell(80, 3.8, "Facture ".$this->invoice['invoicenumber']." du ".date("d/m/Y", strtotime($this->invoice['date'])), 0, "L");
		$pdf->SetXY(120, $hl);
		$pdf->MultiCell(80, 3.8, "Page $page sur {nb}", 0, "R");
		$hl+=10;
		$this->pdf_add_table_header(&$hl, $pdf);
		$pdf->SetFont($GFONT, "", 10);
	    }

	    $pdf->SetXY(20, $hl);
	    $pdf->MultiCell(100, 3.8, $v['ref'], 0, "L");
	    $pdf->SetXY(45, $hl);
	    $pdf->MultiCell(85, 3.8, $v['designation'], 0, "L");
	    $linecount=(int)(($pdf->GetY() - $hl + 0.5) / 3.8);
	    
	    $pdf->SetXY(130, $hl);
	    $pdf->MultiCell(10, 3.8, $v['quantity'], 0, "C");
	    $pdf->SetXY(140, $hl);
	    $pdf->MultiCell(25, 3.8, money_format("%.2n", $v['unitprice']), 0, "R");
	    $pdf->SetXY(165, $hl);
	    $pdf->MultiCell(25, 3.8, money_format("%.2n", $v['quantity'] * $v['unitprice']), 0, "R");
	    $tht+=$v['quantity'] * $v['unitprice'];
	    $hl+=4 + ($linecount * 3.8);
	}
	
	if ($page == 1) {
	    if ($hl < 200) $hl=200;
	} else {
	    //if ($hl < 100) $hl=100;
	}
	

	// add the table right footer
	$hl+=4;
	$hll=$hl;
	$left=120;
	$pdf->SetFont($GFONT, "", 10);
	$pdf->SetXY($left, $hl);
	$pdf->MultiCell(160-$left-0.5, 4.5, "Montant Total H.T", 0, "R", 1);
	$pdf->SetXY(160, $hl);
	$pdf->MultiCell(30, 4.5, money_format("%.2n", $tht), 0, "R", 1);
	$hl+=5;
	$pdf->SetXY($left, $hl);
	$pdf->MultiCell(160-$left-0.5, 4.5, "TVA 19.6%", 0, "R", 1);
	$pdf->SetXY(160, $hl);
	$pdf->MultiCell(30, 4.5, money_format("%.2n", $tht * 0.196), 0, "R", 1);
	$hl+=5;
	$pdf->SetXY($left, $hl);
	$pdf->MultiCell(160-$left-0.5, 4.5, "Montant Total TTC", 0, "R", 1);
	$pdf->SetXY(160, $hl);
	$pdf->MultiCell(30, 4.5, money_format("%.2n", $tht * 1.196), 0, "R", 1);
	$hl+=5;
	$acompte=0.0;
	foreach($this->invoice->get_acomptes() as $v) {
	    $pdf->SetXY($left, $hl);
	    $pdf->MultiCell(160-$left-0.5, 4.5, "Acompte (".date("d/m/Y", strtotime($v['date'])).")", 0, "R", 1);
	    $pdf->SetXY(160, $hl);
	    $pdf->MultiCell(30, 4.5, money_format("%.2n", $v['somme']), 0, "R", 1);
	    $hl+=5;
	    $acompte+=$v['somme'];
	}
	$pdf->SetFont($GFONT, "B", 10);
	$pdf->SetXY($left, $hl);
	$pdf->MultiCell(160-$left-0.5, 4.5, "Net à payer", 0, "R", 1);
	$pdf->SetXY(160, $hl);
	$value=$tht * 1.196;
	if ($value < 0) $value=0;
	$pdf->MultiCell(30, 4.5, money_format("%.2n", $value - $acompte), 0, "R", 1);
	$hl+=5;

	$hl_right=$hl;

	// add the table left footer
	$hl=$hll + 6;
	$pdf->SetFont($GFONT, "B", 10);
	$pdf->SetXY(20, $hl);
	$paymode="";
	foreach($this->invoice->get_paymode('paymode') as $v) $paymode.=($paymode == "" ? "" : ", ").$v;
	$pdf->MultiCell(110, 4.5, "Mode de réglement: ".$paymode, 0, "L", 0);
	$hl+=6;
	$pdf->SetXY(20, $hl);
	$pdf->MultiCell(110, 4.5, "À régler au plus tard le: ".date("d/m/Y", strtotime($this->invoice->get_ref['date_limit'])), 0, "L", 0);
	$hl+=6;
	$pdf->SetXY(20, $hl);
	$pdf->SetFont($GFONT, "", 8.5);
	$pdf->MultiCell(100, 3.5, "Majoration en cas de règlement tardif: 1,5 fois le taux légal (Loi n°1442 du 31/12/1992) avec un minimum de 8,00 ¤ HT par mois de retard", 0, "L", 0);
	$hl+=5;

	$hl_left=$hl;

	$hl=$hl_left > $hl_right ? $hl_left : $hl_right;
	
	$this->pdf_add_rib(&$hl, $pdf);
	// output the pdf
	$pdf->Output();
    }

}


?>