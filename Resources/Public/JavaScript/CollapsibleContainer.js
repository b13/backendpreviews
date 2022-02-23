
define(['jquery', 'TYPO3/CMS/Backend/Storage/Persistent'], function ($, Persistent) {
	'use strict';

	var CollapsibleContainer = {};

	CollapsibleContainer.hide = function(ev, el) {
		var container = el.closest('.js-collapsible-container');
		var containerId = container.data('uid');
		$('.js-colabsible-content', container).hide();
		$('.js-up', container).show();
		$('.js-down', container).hide();
		ev.preventDefault();
		CollapsibleContainer.addCollapsedUid(containerId);
	};

	CollapsibleContainer.show = function(ev, el) {
		var container = el.closest('.js-collapsible-container');
		var containerId = container.data('uid');
		$('.js-colabsible-content', container).show();
		$('.js-down', container).show();
		$('.js-up', container).hide();
		ev.preventDefault();
		CollapsibleContainer.removeCollapsedUid(containerId);
	};

	CollapsibleContainer.getCollapsedUids = function() {
		if (Persistent.isset('tx_collapsible-container')) {
			return JSON.parse(Persistent.get('tx_collapsible-container'));
		}
		return [];
	};

	CollapsibleContainer.addCollapsedUid = function(uid) {
		var existingItems = CollapsibleContainer.getCollapsedUids();
		var index = existingItems.indexOf(uid);
		if (index < 0) {
			existingItems.push(uid);
		}
		Persistent.set('tx_collapsible-container', JSON.stringify(existingItems));
	};

	CollapsibleContainer.removeCollapsedUid = function(uid) {
		var existingItems = CollapsibleContainer.getCollapsedUids();
		var index = existingItems.indexOf(uid);
		if (index > -1) {
			existingItems.splice(index, 1);
		}
		Persistent.set('tx_collapsible-container', JSON.stringify(existingItems));
	};



	CollapsibleContainer.init = function() {

		var existingItems = CollapsibleContainer.getCollapsedUids();
		$('.js-collapsible-container').each(function(key, el) {
			var containerId = $(this).data('uid');
			if (existingItems.indexOf(containerId) >= 0) {
				$('.js-colabsible-content', $(this)).hide();
				$('.js-up', $(this)).show();
				$('.js-down', $(this)).hide();
			}
		});

		$('.js-collapsible-container .js-down').bind('click', function(ev, i) {
			CollapsibleContainer.hide(ev, $(this));
		});
		$('.js-collapsible-container .js-up').bind('click', function(ev, i) {
			CollapsibleContainer.show(ev, $(this));
		});
	};

	$(document).ready(function() {
		CollapsibleContainer.init();
	});
});
