<?php
require_once APPPATH . 'libraries/tcpdf/tcpdf.php';

class MYPDF extends TCPDF
{
    // Page header
    public function Header()
    {
        // Set text color to #033c5a (RGB)
        $this->SetTextColor(3, 60, 90);

        // Set font for header text
        $this->SetFont('helvetica', 'B', 20);
        $this->Ln(6); // Line break

        // Left side: Office address and contact details
        $this->Cell(0, 15, 'B.Singha Roy and Associates', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetY(19);
        $this->SetFont('helvetica', '', 12);

        // Contact Information with Unicode icons
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 15, 'A-26, Amarabati, Sodepur, West Bengal 700110', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln(6); // Line break
        $this->Cell(0, 15, '(+91)7439108284 | info@cabsra.com', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln(6); // Line break
        $this->Cell(0, 15, 'https://cabsra.com', 0, false, 'L', 0, '', 0, false, 'M', 'M');

        // Right side: Logo (Make the width adjust according to the height)
        $image_file = FCPATH . 'assets/img/pdf_logo.jpg'; // Local file path
        if (file_exists($image_file)) {
            // Set the logo height and let TCPDF adjust the width
            $this->Image($image_file, 150, 5, 0, 25, 'JPG', '', 'T', false, 200, '', false, false, 0, false, false, false);
        } else {
            // If image is not found, add a fallback message
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 15, 'B.Singha Roy and Associates', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }

        // Draw a thick line after the header
        $this->Ln(6);                                       // Space before the line
        $this->SetY(36);                                    // Set Y position for the line
        $this->SetLineWidth(0.8);                           // Set the line thickness
        $this->Line(10, $this->GetY(), 200, $this->GetY()); // Draw the line across the page
    }

    // Page footer (Declared only once)
    public function Footer()
    {
        // Set color for footer text to #033c5a (RGB)
        $this->SetTextColor(3, 60, 90);

        // Draw a line before the footer
        $this->SetY(-15);                                   // Position 5mm above the footer content
        $this->SetLineWidth(0.8);                           // Set the line thickness
        $this->Line(10, $this->GetY(), 200, $this->GetY()); // Draw the line across the page

        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font for footer
        $this->SetFont('helvetica', 'I', 8);

        // Left side: Page number
        $this->Cell(90, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'L');

        // Right side: Company name
        $this->Cell(0, 10, 'B.Singha Roy and Associates', 0, 0, 'R');
    }
}
