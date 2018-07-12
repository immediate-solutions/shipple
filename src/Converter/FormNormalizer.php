<?php
namespace ImmediateSolutions\Shipple\Converter;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class FormNormalizer implements NormalizerInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function normalize(ServerRequestInterface $request)
    {
        $data = $request->getParsedBody();

        $files = $this->normalizeFiles($request->getUploadedFiles());

        return array_merge($data, $files);
    }

    private function normalizeFiles(array $files): array
    {
        foreach ($files as $key => $file) {

            if (is_array($file)) {
                $files[$key] = $this->normalizeFiles($file);
            } elseif ($file instanceof UploadedFileInterface) {
                $files[$key] = $file->getClientFilename();
            }
        }

        return $files;
    }
}