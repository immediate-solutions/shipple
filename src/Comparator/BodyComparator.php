<?php
namespace ImmediateSolutions\Shipple\Comparator;

use ImmediateSolutions\Shipple\Converter\FormNormalizer;
use ImmediateSolutions\Shipple\Converter\JsonNormalizer;
use ImmediateSolutions\Shipple\Converter\XmlNormalizer;
use ImmediateSolutions\Shipple\Preference;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Igor Vorobiov<igor.vorobioff@gmail.com>
 */
class BodyComparator extends AbstractComparator
{
    public function compare(array $match, ServerRequestInterface $request): bool
    {
        if (!array_key_exists('body', $match)) {
            return true;
        }

        $template = $match['body'];

        $options = $this->getMergedOptions($match);

        $content = (string) $request->getBody();

        if (($template === null || $template === '') && ($content === '' || $content === null)) {
            return true;
        }

        $bodyType = $options->getBodyType();

        $bodyTypes = [
            Preference::MATCH_BODY_TYPE_JSON,
            Preference::MATCH_BODY_TYPE_FORM,
            Preference::MATCH_BODY_TYPE_TEXT,
            Preference::MATCH_BODY_TYPE_XML
        ];

        if (!in_array($bodyType, $bodyTypes)) {
            return false;
        }

        $method = 'compare' . $this->toCamelCase($bodyType);

        return call_user_func([$this,  $method], $template, $request, $options);

    }

    private function compareText($template, ServerRequestInterface $request, MergedOptions $options): bool
    {
        return $this->interpreter->match($template, (string) $request->getBody());
    }

    private function compareXml($template, ServerRequestInterface $request, MergedOptions $options): bool
    {
        if (!is_array($template)) {
            return false;
        }

        $data = (new XmlNormalizer())->normalize($request);

        return $this->compareNormalized($template, $data, $options);
    }

    private function compareJson($template, ServerRequestInterface $request, MergedOptions $options): bool
    {
        $data = (new JsonNormalizer())->normalize($request);

        if ($data === null && trim((string) $request->getBody()) !== 'null') {
            return false;
        }

        return $this->compareItem($template, $data, $options);
    }

    private function compareItem($template, $data, MergedOptions $options): bool
    {
        if (!is_array($data)) {
            return $this->interpreter->match($template, $data);
        }

        if (is_array($data) && !is_array($template))  {
            return false;
        }

        return $this->compareNormalized($template, $data, $options);
    }

    private function compareForm($template, ServerRequestInterface $request, MergedOptions $options): bool
    {
        if (!is_array($template)) {
            return false;
        }

        $data = (new FormNormalizer())->normalize($request);

        return $this->compareNormalized($template, $data, $options);
    }

    private function compareNormalized(array $template, array $data, MergedOptions $options): bool
    {
        if ($this->isCollection($template) && !$this->isCollection($data)) {
            return false;
        }

        if (!$this->isCollection($template) && $this->isCollection($data)) {
            return false;
        }

        if ($this->isCollection($template)) {
            return $this->compareCollections($template, $data, $options);
        }

        list($template, $data) = $this->reduceCollections($template, $data, $options);

        $template = $this->normalize($template);

        $data = $this->normalize($data);

        return $this->compareByScope($template, $data, $options);
    }

    private function compareByScope(array $template, array $data, MergedOptions $options): bool
    {
        $method = 'compareBy'. $this->toCamelCase($options->getBodyScope());

        return call_user_func([$this, $method], $template, $data);
    }

    private function compareByStrict(array $template, array $data): bool
    {
        if (count($template) !== count($data)) {
            return false;
        }

        return $this->matchTemplateByData($template, $data);
    }

    private function compareBySoft(array $template, array $data): bool
    {
        return $this->matchTemplateByData($template, $data, true);
    }

    private function compareByPartial(array $template, array $data): bool
    {
        if (count($template) > count($data)) {
            return false;
        }

        return $this->matchTemplateByData($template, $data);
    }

    private function compareByOptional(array $template, array $data): bool
    {
        if (count($data) > count($template)) {
            return false;
        }

        foreach ($data as $key => $value)
        {
            if (!$this->matchDataByTemplate($key, $value, $template)) {
                return false;
            }
        }

        return true;
    }

    private function flat(array $data, $prefix = '')
    {
        $result = [];

        foreach ($data as $key => $value) {

            if ($value && is_array($value)) {

                $result = array_merge($result, $this->flat($value, $prefix.$key.'.'));

            } else {
                $result[$prefix.$key] = $value;
            }
        }

        return $result;

    }

    private function reduceCollections(array $template, array $data, MergedOptions $options): array
    {
        foreach ($template as $templateKey => $templateValue) {

            if ($this->isCollection($templateValue)) {
                $matchedKeys = $this->searchMatchedKeys($templateKey, $data);

                foreach ($matchedKeys as $matchedKey) {
                    if ($this->isCollection($data[$matchedKey])
                        && $this->compareCollections($templateValue, $data[$matchedKey], $options)) {
                        $template[$templateKey] = true;
                        $data[$matchedKey] = true;
                    }
                }
            }
        }


        return [$template, $data];
    }

    private function compareCollections(array $template, array $data, MergedOptions $options): bool
    {
        $method = 'compareCollectionsBy'. $this->toCamelCase($options->getBodyScope());

        return call_user_func([$this, $method], $template, $data, $options);
    }

    private function compareCollectionsByStrict(array $template, array $data, MergedOptions $options): bool
    {
        if (count($template) !== count($data)) {
            return false;
        }

        return $this->compareTemplateCollectionsByDataCollections($template, $data, $options);
    }

    private function compareCollectionsBySoft(array $template, array $data, MergedOptions $options): bool
    {
        return $this->compareTemplateCollectionsByDataCollections($template, $data, $options, true);
    }

    private function compareCollectionsByPartial(array $template, array $data, MergedOptions $options): bool
    {
        if (count($template) > count($data)) {
            return false;
        }

        return $this->compareTemplateCollectionsByDataCollections($template, $data, $options);
    }

    private function compareCollectionsByOptional(array $template, array $data, MergedOptions $options): bool
    {
        if (count($data) > count($template)) {
            return false;
        }

        foreach ($data as $dataItem) {
            if (!$this->checkDataItemInTemplateCollection($dataItem, $template, $options)) {
                return false;
            }
        }

        return true;
    }

    private function compareTemplateCollectionsByDataCollections(
        array $template, array $data, MergedOptions $options, bool $soft = false): bool
    {
        foreach ($template as $templateItem) {
            if (!$this->checkTemplateItemInDataCollection($templateItem, $data, $options)) {
                return $soft;
            }
        }

        return !$soft;
    }

    private function checkTemplateItemInDataCollection($templateItem, array $data, MergedOptions $options): bool
    {
        foreach ($data as $dataItem) {
            if ($this->compareItem($templateItem, $dataItem, $options)) {
                return true;
            }
        }

        return false;
    }

    private function checkDataItemInTemplateCollection(
        $dataItem, array $template, MergedOptions $options): bool
    {
        foreach ($template as $templateItem) {
            if ($this->compareItem($templateItem, $dataItem, $options)){
                return true;
            }
        }

        return false;
    }

    private function normalize(array $data): array
    {
        $data = $this->flat($data);

        ksort($data);

        return $data;
    }

    private function searchMatchedKeys(string $key, array $data): array
    {
        if (array_key_exists($key, $data) ) {
            return [$key];
        }

        $result = [];

        foreach (array_keys($data) as $dataKey) {
            if ($this->interpreter->match($key, $dataKey)) {
                $result[] = $dataKey;
            }
        }

        return $result;
    }

    private function matchValueByMatchedKeys($value, array $data, array $matchedKeys): bool
    {
        foreach ($matchedKeys as $matchedKey) {
            if ($this->interpreter->match($value, $data[$matchedKey])) {
                return true;
            }
        }
        return false;
    }

    private function matchDataByTemplate(string $dataKey, $dataValue, array $template): bool {

        foreach ($template as $templateKey => $templateValue) {

            if (!array_key_exists($dataKey, $template) && !$this->interpreter->match($templateKey, $dataKey)) {
                continue ;
            }

            if ($this->interpreter->match($templateValue, $dataValue)) {
                return true;
            }
        }

        return false;
    }

    private function matchTemplateByData(array $template, array $data, bool $soft = false)
    {
        foreach ($template as $key => $value) {

            $matchedKeys = $this->searchMatchedKeys($key, $data);

            if (!$matchedKeys) {
                return false;
            }

            if (!$this->matchValueByMatchedKeys($value, $data, $matchedKeys)) {
                return false;
            }

            if ($soft) {
                return true;
            }
        }

        return true;
    }

    private function toCamelCase(string $source): string
    {
        return preg_replace_callback('/(?:^|-)(.?)/', function($matches) {
            return strtoupper($matches[1]);
        }, $source);
    }


    private function isCollection($data) : bool
    {
        if (!is_array($data)) {
            return false;
        }

        $counter = 0;

        foreach (array_keys($data) as $key) {
            if ($key !== $counter) {
                return false;
            }

            $counter ++;
        }

        return true;
    }
}