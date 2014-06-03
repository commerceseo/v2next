<?php
/*
	$Id: modifier.truncate.php 645 2013-09-30 21:16:12Z akausch $
	
	Author - Sebastian Schramm
	(c) 2010 commerce:SEO - commerce-seo.de
	
	cseo_trunecat.inc.php

*/

function smarty_modifier_truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = true) {
		if($length < 1)
			$length = 150;
        if ($considerHtml) {
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            
            foreach ($lines as $line_matchings) {
                if (!empty($line_matchings[1])) {
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    $truncate .= $line_matchings[1];
                }
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length+$content_length > $length) {
                    $left = $length - $total_length;
                    $entities_length = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                if($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
		if (!$exact) {
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;
        if($considerHtml) {
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
		return $truncate;
	}
