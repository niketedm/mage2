<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model;

use Magefan\Blog\Model\Url;

/**
 * Class Blog Plus Url Resolver
 */
class UrlResolver extends \Magefan\Blog\Model\UrlResolver
{
    /**
     * @param string $path
     * @return array
     */
    public function resolve($path)
    {
        if (!$this->url->isAdvancedPermalinkEnabled()) {
            return parent::resolve($path);
        }

        $identifier = trim($path, '/');
        $identifier = urldecode($identifier);
        $identifierLen = strlen($identifier);

        $basePath = trim($this->url->getBasePath(), '/');

        if ($identifier == $basePath) {
            return ['id' => 1, 'type' => Url::CONTROLLER_INDEX];
        } else {
            $schemas = $this->url->getPathChemas();
            foreach ($schemas as $controllerName => $schema) {
                $schema = trim($schema, '/');
                $startVar = strpos($schema, '{');
                $endVar = strrpos($schema, '}');

                if (false === $startVar || false === $endVar) {
                    continue;
                }

                if (substr($schema, 0, $startVar) != substr($identifier, 0, $startVar)) {
                    continue;
                }

                $endVar++;
                if (substr($schema, $endVar) != substr($identifier, $identifierLen - (strlen($schema) - $endVar))) {
                    continue;
                }

                $subSchema = substr($schema, $startVar, $endVar - $startVar);
                $subIdentifier = substr(
                    $identifier,
                    $startVar,
                    $identifierLen - (strlen($schema) - $endVar) - $startVar
                );

                $pathInfo = explode('/', $subIdentifier);

                $subSchema = explode('/', $subSchema);
                if (($subSchema[0] == '{{blog_route}}') && (strpos($subIdentifier, $basePath) === false)) {
                    continue;
                }

                if ('{' != $subSchema[0][0] && $subSchema[0] != $pathInfo[0]) {
                    continue;
                }

                if ($subSchema[count($subSchema) - 1] == '{{url_key}}') {
                    switch ($controllerName) {
                        case 'post':
                        case 'category':
                        case 'tag':
                        case 'author':
                            $method = '_get' . ucfirst($controllerName) . 'Id';
                            $id = $this->$method($pathInfo[count($pathInfo) - 1]);

                            if (!$id && !empty($pathInfo[count($pathInfo) - 2])) {
                                $id = $this->$method(
                                    $pathInfo[count($pathInfo) - 2] . '/' .
                                    $pathInfo[count($pathInfo) - 1]
                                );
                            }

                            if ($id) {
                                $factory = $controllerName . 'Factory';
                                $model = $this->$factory->create()->load($id);
                                if ($model->getId()) {
                                    $path = $this->url->getUrlPath($model, $controllerName);
                                    $path = trim($path, '/');

                                    if ($path == $identifier) {
                                        return ['id' => $id, 'type' => $controllerName];
                                    }
                                }
                            }
                            break;
                        case 'archive':
                            $date = $pathInfo[count($pathInfo) - 1];
                            if ($this->_isArchiveIdentifier($date)) {
                                $path = $this->url->getUrlPath($date, $controllerName);
                                $path = trim($path, '/');
                                if ($path == $identifier) {
                                    return ['id' => $date, 'type' =>$controllerName];
                                }
                            }
                            break;
                        case 'search':
                            $q = '';
                            for ($x = 1; $x <=4; $x++) {
                                if (!isset($pathInfo[count($pathInfo) - $x])) {
                                    break;
                                }
                                $q = $pathInfo[count($pathInfo) - $x] . ($q ? '/' : '') . $q;
                                $path = $this->url->getUrlPath($q, $controllerName);
                                $path = trim($path, '/');
                                if ($path == $identifier) {
                                    return ['id' => $q, 'type' => $controllerName];
                                }
                            }
                        default:
                            /* do nothing */
                    }
                }
            }
        }
    }
}
