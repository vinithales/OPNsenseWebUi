<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class UserCredentialsPdfService
{
    /**
     * Gera PDF com credenciais dos usuários importados
     * LGPD: PDF é gerado temporariamente e não é armazenado permanentemente
     *
     * @param array $credentials Array com dados dos usuários (ra, email, password, user_type)
     * @return \Illuminate\Http\Response
     */
    public function generateCredentialsPdf(array $credentials)
    {
        // Prepara dados para o PDF
        $data = [
            'credentials' => $credentials,
            'generated_at' => now()->format('d/m/Y H:i:s'),
            'total' => count($credentials),
        ];

        // Gera PDF usando a view
        $pdf = Pdf::loadView('pdf.user-credentials', $data);

        // Configura orientação e tamanho
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Retorna PDF para download
     *
     * @param array $credentials
     * @return \Illuminate\Http\Response
     */
    public function downloadCredentialsPdf(array $credentials)
    {
        $pdf = $this->generateCredentialsPdf($credentials);
        $filename = 'credenciais_usuarios_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Retorna PDF para visualização no navegador
     *
     * @param array $credentials
     * @return \Illuminate\Http\Response
     */
    public function streamCredentialsPdf(array $credentials)
    {
        $pdf = $this->generateCredentialsPdf($credentials);

        return $pdf->stream();
    }
}
