<?php
namespace ImmediateSolutions\Shipple\Converter;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class XmlNormalizer implements NormalizerInterface
{
    /**
     * @param  ServerRequestInterface $request
     * @return mixed
     */
    public function normalize(ServerRequestInterface $request)
    {
        $data = (string) $request->getBody();

        $root = new \SimpleXMLIterator($data);

        return $this->fromNode($root);
    }

    private function fromNode(\SimpleXMLElement $node): array
    {
        $result = [
            'name' => $node->getName(),
            'text' => (string) $node,
            'attributes' => $this->fromAttributes($node->attributes()),
            'children' => $this->fromChildren($node->children())
        ];

        return $result;
    }

    private function fromAttributes(\SimpleXMLElement $attributes): array
    {
        $result = [];

        foreach ($attributes as $name => $value) {

            $result[] = [
                'name' => $name,
                'value' => $value
            ];
        }

        return $result;
    }

    private function fromChildren(\SimpleXMLElement $children): array
    {
        if ($children->count() === 0) {
            return [];
        }

        $result = [];

        foreach ($children as $child){
            $result[] = $this->fromNode($child);
        }

        return $result;
    }
}