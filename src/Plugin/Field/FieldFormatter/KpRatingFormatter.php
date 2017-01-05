<?php

/**
 * @file
 * Contains \Drupal\kp_rating\Plugin\Field\FieldFormatter\KpRatingFormatter.
 */

namespace Drupal\kp_rating\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/** *
 * @FieldFormatter(
 *   id = "kp_rating_formatter",
 *   label = @Translation("Kinopoisk rating"),
 *   field_types = {
 *     "text",
 *     "string"
 *   }
 * )
 */
class KpRatingFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = array();

    $errno = NULL;
    $errstr = '';
    $is_available = @fsockopen('rating.kinopoisk.ru', 80, $errno, $errstr, 2);

    if (!$is_available) {
      return [];
    }

    foreach ($items as $delta => $item) {
      $film_id = $item->value;

      $xml_url = "https://rating.kinopoisk.ru/{$film_id}.xml";
      $xml = simplexml_load_file($xml_url);

      $kp_vote = $xml->kp_rating['num_vote'];
      $kp_rating = $xml->kp_rating;
      $imdb_vote = $xml->imdb_rating['num_vote'];
      $imdb_rating = $xml->imdb_rating;

      $element[$delta] = [
        '#theme'       => 'kp_formatter',
        '#kp_vote'     => $kp_vote,
        '#kp_rating'   => $kp_rating,
        '#imdb_vote'   => $imdb_vote,
        '#imdb_rating' => $imdb_rating,
      ];
    }

    return $element;
  }

}
