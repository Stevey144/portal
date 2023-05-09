<?php

/**
 * @package    Joomla! Volunteers
 * @copyright  Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Volunteers\Site\Helper\VolunteersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects
HTMLHelper::_('jquery.framework');
// Import CSS
try {
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useStyle('com_volunteers.j3template')
        ->useStyle('com_volunteers.frontend')
        ->useScript('com_volunteers.google_maps')
        ->useScript('com_volunteers.markerclusterer')
        ->useScript('com_volunteers.oms');
} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}


?>

<div class="row-fluid">
    <img style="width: 100%" src="media/com_volunteers/images/volunteer-header.png" alt="Become a Joomla! contributor">
</div>
<br>
<div class="row-fluid">
    <div class="span6">
        <h2>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_HOW_TITLE'); ?>
        </h2>
        <p>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_HOW_DESC'); ?>
        </p>
        <p>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_HOW_ACTION'); ?>
        </p>
        <p>
            <a href="<?php echo Route::_('index.php?option=com_volunteers&view=roles'); ?>" class="btn"><col-
                    class="icon-chevron-right"></col->
                <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_HOW_BUTTON'); ?>
            </a>
        </p>
    </div>
    <div class="span6">
        <h2>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_WHY_TITLE'); ?>
        </h2>
        <p>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_WHY_DESC'); ?>
        </p>
        <p>
            <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_WHY_ACTION'); ?>
        </p>
        <p>
            <a href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteers'); ?>" class="btn"><col-
                    class="icon-chevron-right"></col->
                <?php echo Text::_('COM_VOLUNTEERS_HOME_INTRO_WHY_BUTTON'); ?>
            </a>
        </p>
    </div>
</div>
<br>
<div class="row-fluid">
    <h2>
        <?php echo Text::_('COM_VOLUNTEERS_LATEST_REPORTS') ?>
    </h2>
    <?php if (!empty($this->reports)) {
        foreach ($this->reports as $i => $item): ?>
            <div class="row report">
                <div class="span2">

                    <a
                        href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $item->volunteer_id) ?>">
                        <?php echo VolunteersHelper::image($item->volunteer_image, 'large', false, $item->volunteer_name); ?>
                    </a>
                </div>
                <div class="span10">
                    <h3 class="report-title">
                        <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $item->id) ?>">
                            <?php echo ($item->title); ?>
                        </a>
                    </h3>
                    <p class="muted">
                        <?php echo Text::_('COM_VOLUNTEERS_BY') ?>
                        <a
                            href="<?php echo Route::_('index.php?option=com_volunteers&view=volunteer&id=' . $item->volunteer_id) ?>"><?php echo $item->volunteer_name; ?></a>
                        <?php echo Text::_('COM_VOLUNTEERS_ON') ?>
                        <?php echo VolunteersHelper::date($item->created, 'Y-m-d H:i'); ?>
                        <?php echo Text::_('COM_VOLUNTEERS_IN') ?>
                        <a href="<?php echo $item->link; ?>"><?php echo $item->name; ?></a>
                    </p>
                    <p>
                        <?php echo StringHelper::truncate(strip_tags(trim($item->description)), 380); ?>
                    </p>
                    <a href="<?php echo Route::_('index.php?option=com_volunteers&view=report&id=' . $item->id) ?>" class="btn">
                        <col- class="icon-chevron-right"></col->
                        <?php echo Text::_('COM_VOLUNTEERS_READ_MORE') ?>&nbsp;
                        <?php echo HtmlHelper::_('string.truncate', $item->title, 55); ?>
                    </a>
                </div>
            </div>
            <hr>
        <?php endforeach;
    }
    ?>
    <a class="btn btn-large btn-block"
        href="<?php echo Route::_('index.php?option=com_volunteers&view=reports'); ?>"><?php echo Text::_('COM_VOLUNTEERS_READ_MORE_REPORTS') ?></a>
</div>
<br>
<div class="row-fluid">
    <h2>
        <?php echo count($this->markers) . ' ' . Text::_('COM_VOLUNTEERS_VOLUNTEERS_WORLD') ?>
    </h2>
    <div id="map-canvas"></div>
</div>
<script>
    function initialise() {
        var mapOptions = {
            zoom: 2,
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL
            },
            center: { lat: 25, lng: 15 },
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            panControl: false,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            overviewMapControl: false,
            rotateControl: false,
            draggable: !("ontouchend" in document)
        },
            mcOptions = {
                maxZoom: 14,
                styles: [{
                    height: 53,
                    url: "media/com_volunteers/images/m1.png",
                    width: 53
                },
                {
                    height: 56,
                    url: "media/com_volunteers/images/m2.png",
                    width: 56
                },
                {
                    height: 66,
                    url: "media/com_volunteers/images/m3.png",
                    width: 66
                },
                {
                    height: 78,
                    url: "media/com_volunteers/images/m4.png",
                    width: 78
                },
                {
                    height: 90,
                    url: "media/com_volunteers/images/m5.png",
                    width: 90
                }
                ]
            }

        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
        var oms = new OverlappingMarkerSpiderfier(map, { keepSpiderfied: true, circleFootSeparation: 100 });

        map.addListener('click', function () {
            map.set('draggable', true);
        });

        var markers = [];
        var bounds = new google.maps.LatLngBounds();
        var infoWindow = new google.maps.InfoWindow();
        var locations = [<?php echo implode(',', $this->markers) ?>];

        var icon = {
            url: "media/com_volunteers/images/joomla.png", // url
            scaledSize: new google.maps.Size(50, 50), // scaled size
            origin: new google.maps.Point(0, 0), // origin
            anchor: new google.maps.Point(25, 25) // anchor
        };

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i].lat, locations[i].lng),
                map: map,
                icon: icon
            });

            google.maps.event.addListener(marker, 'click', (function (marker, i, infoWindow) {
                return function () {
                    infoWindow.setContent('<div style="width:200px;"><img width="40" class="pull-left" style="padding-right: 10px" src="' + locations[i].image + '" /><a href="' + locations[i].url + '">' + locations[i].title + '</a></div>');
                    infoWindow.open(map, marker);
                }
            })(marker, i, infoWindow));

            markers.push(marker);
            bounds.extend(marker.position);
            oms.addMarker(marker);
        }

        var markerCluster = new MarkerClusterer(map, markers, mcOptions);

        var styles = [
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [
                    { lightness: 100 },
                    { visibility: "simplified" }
                ]
            }, {
                featureType: "road",
                elementType: "labels",
                stylers: [
                    { visibility: "simplified" }
                ]
            }, {
                featureType: "poi",
                elementType: "labels",
                stylers: [
                    { visibility: "on" }
                ]
            }, {
                featureType: "poi.business",
                elementType: "labels",
                stylers: [
                    { visibility: "off" }
                ]
            }, {
                featureType: "water",
                elementType: "labels",
                stylers: [
                    { visibility: "on" }
                ]
            }
        ];
        map.fitBounds(bounds);
        map.setOptions({ styles: styles });
    }

    google.maps.event.addDomListener(window, 'load', initialise);
</script>
