(function( $ ) {
	'use strict';

	/**
	 * All of the code for the admin-facing JavaScript source
	 */

	$( document ).ready(function() {
		var $dropzoneArray = [];
		var $obj = {};
		var $json = [];
		var $orderList = [];
		loadDropzoneArray();

		if( $('#MESSAGE_BUSINESS_FORMBUILDEROPTIONS').val() ) {
			$json = JSON.parse( $('#MESSAGE_BUSINESS_FORMBUILDEROPTIONS').val() );			
		}

		/**
		 * @description checks if an array of items exists and if so it loads all those items to the dropzone
		 */
		function loadDropzoneArray() {

			$( '#dropzoneItems li' ).each(function() {
				var itemId = $( this ).attr('item-id');
				$dropzoneArray.push(itemId);
				$( '#availableItems li[item-id="' + itemId + '"]' ).addClass('cancelled');
			});
			getItemIdsDropped();		
		}

		/**
		 * @description removes the selected item from the items array
		 */
		function removeItem() {
			
			var itemToRemove = $( this ).parents( 'li' );
			var itemId = itemToRemove.attr('item-id');
			
			itemToRemove.remove();
			$dropzoneArray.splice( $.inArray( itemId, $dropzoneArray ), 1 );
			$( '#availableItems li[item-id="' + itemId + '"]' ).removeClass('cancelled');
			removeFieldToFormBuilderOptions(itemId);
		}

		/**
		 * @description shows and hides the item's content depending on the click event
		 * @param {*} element
		 */
		function toggleExpandIcon(element) {

			element.parents( '.fieldholder' ).children( '.field-content' ).toggle();
			if ( $( element ).text() == 'expand_more' ) {
				$( element ).html( 'expand_less' );
			} else {
				$( element ).html( 'expand_more' );
			}
		}

		/**
		 * @description add item object to the json items array and update the items positions
		 * @param {*} item 
		 */
		function addFieldToFormBuilderOptions(item) {

			var itemId = item.attr('item-id').replace(/["`'<>\t\r\n]+/g, "");
			var itemShortcode = item.attr('item-shortcode').replace(/["`'<>\t\r\n]+/g, "");
			var itemName = item.attr('item-name').replace(/["`'<>\t\r\n]+/g, "");
			var itemLabel = item.find('input[name="input-label-' + itemId + '"]').val().replace(/["`'<>\t\r\n]+/g, "");
			var itemOptional = item.find('input[name="input-optional-' + itemId + '"]').is(':checked');
			var itemValue = '';
			var itemHidden = item.find('input[name="input-hidden-' + itemId + '"]').is(':checked');
			var itemPosition = itemId;
			var itemUnicityKey = false;
			var field = { id: itemId, shortcode: itemShortcode, name: itemName, label: itemLabel, optional: itemOptional, position: itemPosition, value: itemValue, hidden: itemHidden, unicityKey: itemUnicityKey };
			$json.push({ id: field.id, shortcode: field.shortcode, name: field.name, label: field.label, optional: field.optional, position: field.position, value: field.value, hidden: field.hidden, unicityKey: field.unicityKey });
			getItemIdsDropped();
			updateFieldPosition();
		}

		/**
		 * @description delete an item from the json items array and update the items positions
		 * @param {*} fieldId 
		 */
		function removeFieldToFormBuilderOptions(fieldId) {

			$json = $.grep($json, function(e){ 
				return e.id != fieldId; 
			});
			getItemIdsDropped();
			updateFieldPosition();
		}

		/**
		 * @description update item label and optional properties
		 */
		function editFieldToFormBuilderOptions(itemId) {

			var fieldToUpdate = $.grep($json, function(obj){return obj.id === itemId;})[0];
			fieldToUpdate.label = $('input[name="input-label-' + itemId + '"]').val().replace(/["`'<>\t\r\n]+/g, "");
			
			fieldToUpdate.optional = $('input[name="input-optional-' + itemId + '"]').is(':checked');

			// set hidden property
			fieldToUpdate.hidden = $('input[name="input-hidden-' + itemId + '"]').is(':checked');

			// add field's value if it exists
			if( $('input[name="input-value-' + itemId + '"]').is(':visible') ) {
				fieldToUpdate.value = $('input[name="input-value-' + itemId + '"]').val().replace(/["`'<>\t\r\n]+/g, "");
			}

			updateMbFormBuilderOptions();
		}

		/**
		 * @description affects the json objects array stringified to the MESSAGE_BUSINESS_FORMBUILDEROPTION hidden input
		 */
		function updateMbFormBuilderOptions() {

			$('#MESSAGE_BUSINESS_FORMBUILDEROPTIONS').val( JSON.stringify($json) );
		}

		/**
		 * @description update field position of the item in the array
		 */
		function updateFieldPosition() {

			$json.forEach(element => {
				var fieldToUpdate = $.grep($json, function(obj){return obj.id === element.id;})[0];
				fieldToUpdate.position = $orderList.indexOf(element.id);
			});
			updateMbFormBuilderOptions();
		}

		/**
		 * @description get array of all item ids dropped on the dropzone
		 */
		function getItemIdsDropped() {

			$orderList = $('#dropzoneItems li').map(function(i) { return $(this).attr('item-id'); }).get();
		}

		$( document ).on('change', 'input', function() {
			var fieldId = $( this ).parents('li').attr('item-id');
			editFieldToFormBuilderOptions(fieldId);
		});

		$( '#availableItems li' ).draggable({
			connectToSortable: "#dropzoneItems",
			revert: "invalid",
			helper: "clone",
			handle: ".fieldholder",
			cancel: '.cancelled',
		});

		$( '.dropzone' ).droppable({
			accept: '#availableItems li',
			drop: function(event, ui) {

				$obj = ui.draggable.clone();

				var draggable = ui.draggable[0];
				
				if( !$( this ).find('li') || $.inArray( $obj.attr('item-id'), $dropzoneArray ) == -1 ) {

                    console.log( 'dropped' );

                    $( this )
					.find( 'ul' )
					.append( $obj )
					.css( { "height": "auto", "width": "100%", "top": "auto", "bottom": "auto" } );
					$dropzoneArray.push( $obj.attr('item-id') );
					$( '#availableItems li[item-id="' + $obj.attr('item-id') + '"]' ).addClass('cancelled');
					$obj
					.addClass( 'dragged' )					
					.css( { "position": "initial", "height": "auto", "width": "100%", "min-width": "200px", "top": "auto", "bottom": "auto" } )
					.find( '.item-options' )
					.html(
						'<i class="material-icons expand-item">expand_more</i>' +
						'<i class="material-icons remove-item">close</i>'
					);
					
					// add item to json array of fields
					addFieldToFormBuilderOptions($obj);
				
					// fill the order list array and update the current item's position attribute
					updateFieldPosition();

				}

			}
		});

		$( '#dropzoneItems' ).sortable({
			placeholder: "ui-sortable-placeholder",
			handle: ".fieldbar",
			forcePlaceholderSize: true,
			revert: true,
			distance: 10,
            sort: function(event, ui) {

				var orderList = $( this ).sortable('toArray');
				
				$( ui.item )
				.addClass( 'dragged' )
				.css( { "height": "auto", "min-width": "200px", "top": "auto", "bottom": "auto" } );

				if( $( ui.item ).attr('item-unicitykey') == true ) {
					$( ui.item )
					.find( '.item-options' )
					.html( '<i class="material-icons expand-item">expand_more</i>' );
				} else {
					$( ui.item )
					.find( '.item-options' )
					.html(
						'<i class="material-icons expand-item">expand_more</i>' +
						'<i class="material-icons remove-item">close</i>'
					);
				}
			},
			receive: function(event, ui) {

				var item = ui.sender;
				$( '.dragged' )
				.css( { "height": "auto", "width": "100%", "top": "auto", "bottom": "auto" } );
				if( !$( this ).find('li') || $.inArray( item.attr('item-id'), $dropzoneArray ) == -1 ) {
					$dropzoneArray.push( item.attr('item-id') );
					$( '#availableItems li[item-id="' + item.attr('item-id') + '"]' ).addClass('cancelled');
					
					addFieldToFormBuilderOptions(item);
				}
			},
			update: function(event, ui) {

				$( ui.item )
				.css( { "width": "" } );

				getItemIdsDropped();
				updateFieldPosition();
			}
        });

		$(document).on('click', '.remove-item', removeItem);


		$(document).on('click', '.expand-item', function(event) {
			var item = $(this);
			toggleExpandIcon(item);
			
		});

		$(document).on('click', 'input[name^="input-hidden-"]', function() {
			var idString = $(this).attr('id').split('-');
			var id = idString[idString.length - 1];

			if( $(this).is(':checked') ) {
				$('.block-value-' + id).show();
			} else {
				$('.block-value-' + id).hide();
			}
		});

	});

})( jQuery );
