<?php
// Copyright 2020 Catchpoint Systems Inc.
// Use of this source code is governed by the Polyform Shield 1.0.0 license that can be
// found in the LICENSE.md file.

require_once __DIR__ . '/../waterfall.inc';

class WaterfallViewHtmlSnippet {

  private $testInfo;
  private $stepResult;
  private $requests;

  /**
   * WaterfallViewHtmlSnippet constructor.
   * @param TestInfo $testInfo
   * @param TestStepResult $stepResult
   */
  public function __construct($testInfo, $stepResult) {
    $this->testInfo = $testInfo;
    $this->stepResult = $stepResult;
    $this->requests = $stepResult->getRequestsWithInfo(true, true);
  }

  public function create() {
    $out = $this->_createLegend();

    $label = $this->stepResult->readableIdentifier($this->testInfo->getUrl());
    $out .= CreateWaterfallHtml($label, $this->requests->getRequests(), $this->testInfo->getId(),
      $this->stepResult->getRunNumber(), $this->stepResult->isCachedRun(), $this->stepResult->getRawResults(),
      '', $this->stepResult->getStepNumber());

    $urlGenerator = $this->stepResult->createUrlGenerator("", false);
    $out .=  "<br><a href=\"" . $urlGenerator->stepDetailPage("customWaterfall", "width=930") . "\">customize waterfall</a> &#8226; ";
    $out .=  "<a id=\"view-images\" href=\"" . $urlGenerator->stepDetailPage("pageimages") . "\">View all Images</a>";
    $out .=  " &#8226; <a id=\"http2-dependencies\" href=\"" . $urlGenerator->stepDetailPage("http2_dependencies") . "\">View HTTP/2 Dependency Graph</a>";
    $out .=  " &#8226; <a href=\"" . $urlGenerator->filmstripView() . "\">Filmstrip</a>";
    return $out;
  }

    private function _legendBarTableCell($color, $label, $width, $dashed = false) {
    $style = "style=\"width: {$width}px;";
    if ($dashed) {
      $style .= " background-image: linear-gradient(0deg, $color 25%, #ffffff 25%, #ffffff 50%, $color 50%, $color 75%, #ffffff 75%, #ffffff 100%);";
    } else {
      $style .= " background-color: $color;";
    }
    $style .= '"';
    return "<td><table><tr><td><div class=\"bar\" " . $style . "></div></td><td>" . $label . "</td></tr></table></td>\n";
  }

  private function _legendHighlightTableCell($color, $label) {
    $style = "style=\"background-color:" . $color . "\"";
    return "<td style=\"vertical-align:middle; padding: 4px;\"><div ". $style . ">&nbsp;" . $label . "&nbsp;</div></td>";
  }

  private function _createLegend() {
    $out = '';
    if (!GetSetting('mime_waterfalls', 1)) {
      $out .= '<table border="1" bordercolor="silver" cellpadding="2px" cellspacing="0" ' .
        'style="width:auto; font-size:11px; margin-left:auto; margin-right:auto;">';
      $out .= "\n<tr>\n";
      $out .= $this->_legendBarTableCell("#1f7c83", "DNS Lookup", 15);
      $out .= $this->_legendBarTableCell("#e58226", "Initial Connection", 15);
      if ($this->requests->hasSecureRequests()) {
        $out .= $this->_legendBarTableCell("#c141cd", "SSL Negotiation", 15);
      }
      $out .= $this->_legendBarTableCell("#1fe11f", "Time to First Byte", 15);
      $out .= $this->_legendBarTableCell("#1977dd", "Content Download", 15);
      $out .= $this->_legendHighlightTableCell("#ffff60", "3xx response");
      $out .= $this->_legendHighlightTableCell("#ff6060", "4xx+ response");
      $out .= "</tr>\n</table>\n";
    }

    $out .= '<table border="1" bordercolor="silver" cellpadding="2px" cellspacing="0" ' .
      'style="width:auto; font-size:11px; margin-left:auto; margin-right:auto; margin-top:11px;">';
    $out .= "\n<tr>\n";
    $out .= $this->_legendBarTableCell("#28BC00", "Start Render", 4);
    if ($this->stepResult->getMetric("aft"))
      $out .= $this->_legendBarTableCell("#FF0000", "Above the Fold", 4);
    if ((float)$this->stepResult->getMetric("domTime"))
      $out .= $this->_legendBarTableCell("#F28300", "DOM Element", 4);
    if ((float)$this->stepResult->getMetric("firstContentfulPaint"))
      $out .= $this->_legendBarTableCell("#39E600", "First Contentful Paint", 4);
    if ((float)$this->stepResult->getMetric("chromeUserTiming.LargestContentfulPaint"))
      $out .= $this->_legendBarTableCell("#008000", "Largest Contentful Paint", 4, true);
    $shifts = $this->stepResult->getMetric("LayoutShifts");
    if ($shifts && is_array($shifts) && count($shifts))
      $out .= $this->_legendBarTableCell("#FF8000", "Layout Shift", 4, true);
    if ((float)$this->stepResult->getMetric("domInteractive"))
      $out .= $this->_legendBarTableCell("#FFC61A", "DOM Interactive", 4);
    if ((float)$this->stepResult->getMetric("domContentLoadedEventStart"))
      $out .= $this->_legendBarTableCell("#D888DF", "DOM Content Loaded", 15);
    if ((float)$this->stepResult->getMetric("loadEventStart"))
      $out .= $this->_legendBarTableCell("#C0C0FF", "On Load", 15);
    $out .= $this->_legendBarTableCell("#0000FF", "Document Complete", 4);
    if (GetSetting('waterfall_show_user_timing') && $this->stepResult->getMetric('userTime'))
      $out .= '<td><table><tr><td><div class="arrow-down"></div></td><td>User Timings</td></tr></table></td>';
    $out .= "</tr>\n</table>\n<br>";
    return $out;
  }
}