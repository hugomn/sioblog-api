<?php

namespace Sioblog\ApiBundle\Twig;

/**
* Extensões twig de internacionalização.
*/
class IntlExtension extends \Twig_Extension {

  private $session;

  private $timezone;

  /**
  * Construtor padrão.
  *
  * @param SecurityContext $security   The security context
  * @param Session         $session    The session
  */
  public function __construct($session) {
    $this->session = $session;
  }

  /**
  * Returns a list of filters to add to the existing list.
  *
  * @return array An array of filters
  */
  public function getFilters()
  {
    return array(new \Twig_SimpleFilter('intldate', array($this, 'intlDateFilter'), array('needs_environment' => true)));
  }

  /**
   * @param env $env           Environment
   * @param DateTime $date     Date
   * @param string $dateFormat Format
   * @param string $timeFormat Time format
   * @param Locale $locale     Locale
   * @param string $timezone   Timezone
   * @param string $format     Format
   */
  public function intlDateFilter($env, $date, $format = null) {
      $this->timezone = $this->session->get('timezone', 'Europe/Berlin');
      $date = twig_date_converter($env, $date, $this->timezone);

      $formatter = \IntlDateFormatter::create(
        null, // locale
        \IntlDateFormatter::SHORT,
        \IntlDateFormatter::SHORT,
        $date->getTimezone()->getName(),
        \IntlDateFormatter::GREGORIAN,
        $format
      );

    return $formatter->format($date->getTimestamp());

  }

  /**
  * Return the extension's name.
  * @return string Extension's name.
  */
  public function getName() {
    return 'sioblog_intl';
  }
}
