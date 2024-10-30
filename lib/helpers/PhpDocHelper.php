<?php

namespace MLMSoft\lib\helpers;

use ReflectionClass;

class PhpDocHelper
{
    public static function getClassProperties(ReflectionClass $reflect)
    {
        $result = [];
        $docComment = $reflect->getDocComment();
        if (trim($docComment) == '') {
            return $result;
        }
        $docComment = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ ]{0,1}(.*)?#', '$1', $docComment);
        $docComment = ltrim($docComment, "\r\n");
        $parsedDocComment = $docComment;
        $lineNumber = 0;
        while (($newlinePos = strpos($parsedDocComment, "\n")) !== false) {
            $lineNumber++;
            $line = substr($parsedDocComment, 0, $newlinePos);

            $matches = array();
            if ((strpos($line, '@') === 0) && (preg_match('#^(@\w+.*?)(\n)(?:@|\r?\n|$)#s', $parsedDocComment, $matches))) {
                $tagDocblockLine = $matches[1];
                $matches2 = array();
                if (!preg_match('#^@(\w+)(\s|$)#', $tagDocblockLine, $matches2)) {
                    break;
                }
                $matches3 = array();
                if (!preg_match('#^@(\w+)\s+([\w|\\\]+)(?:\s+(\$\S+))?(?:\s+(.*))?#s', $tagDocblockLine, $matches3)) {
                    break;
                }
                if ($matches3[1] == 'property') {
                    $name = $matches3[3];
                    $result[$name] = [
                        'type' => $matches3[2],
                        'description' => trim($matches3[4])
                    ];
                }

                $parsedDocComment = str_replace($matches[1] . $matches[2], '', $parsedDocComment);
            }
        }
        return $result;
    }
}