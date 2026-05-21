(function($){
	"use strict"
	$(document).ready(function(){

		// Enable or disable recurring option based on the payments gateway type
		let payment_gateway = $('select[id="ims_basic_settings[ims_payment_method]"]');
		control_recurring_option();
		payment_gateway.on('change', function(){
			control_recurring_option();
		});

		function control_recurring_option(){
			let recurring_option = $('input[name="ims_basic_settings[ims_recurring_memberships_enable]"]');
			
			if('woocommerce'===payment_gateway.val()){
				recurring_option.prop('checked', false);
				recurring_option.attr('disabled', 'disabled');
			} else {
				recurring_option.removeAttr('disabled');
			}
		}

		// Membership Assignment Logic
		if ( typeof imsAssignVars !== 'undefined' ) {
			var packageDetails = imsAssignVars.packageDetails;

			// User selector: load current membership info via AJAX
			$( '#ims_user_id' ).on( 'change', function () {
				var userId        = $( this ).val();
				var infoContainer = $( '#ims_current_user_membership_info' );

				if ( ! userId ) {
					infoContainer.slideUp();
					return;
				}

				infoContainer
					.html( '<p><span class="spinner is-active"></span>' + imsAssignVars.loadingMsg + '</p>' )
					.slideDown();

				$.post(
					ajaxurl,
					{
						action  : 'ims_get_user_membership_info_admin',
						user_id : userId
					},
					function ( response ) {
						if ( response.success ) {
							infoContainer.html( response.data.html );
						} else {
							infoContainer.html( '<p class="description error">' + response.data + '</p>' );
						}
					}
				);
			} );

			// Package selector: show selected package details
			$( '#ims_membership_id' ).on( 'change', function () {
				var packageId     = $( this ).val();
				var infoContainer = $( '#ims_selected_package_info' );

				if ( ! packageId || packageId === 'none' || typeof packageDetails[ packageId ] === 'undefined' ) {
					infoContainer.slideUp();
					return;
				}

				var pkg  = packageDetails[ packageId ];
				var html = '<div class="membership-info-box">';
				html += '<strong>' + imsAssignVars.packageDetailsLabel + '</strong>';
				html += '<table>';
				html += '<tr><td class="label">' + imsAssignVars.titleLabel + '</td><td>' + pkg.title + '</td></tr>';
				html += '<tr><td class="label">' + imsAssignVars.durationLabel + '</td><td>' + pkg.duration + '</td></tr>';
				html += '<tr><td class="label">' + imsAssignVars.allowedLabel + '</td><td>' + pkg.allowed + '</td></tr>';
				html += '<tr><td class="label">' + imsAssignVars.featuredLabel + '</td><td>' + pkg.featured + '</td></tr>';
				html += '</table>';
				html += '</div>';
				html += '<p class="description warning-text">';
				html += '<span class="dashicons dashicons-warning"></span>';
				html += imsAssignVars.warningMsg;
				html += '</p>';

				infoContainer.html( html ).slideDown();
			} );
		}
	});
})(jQuery);