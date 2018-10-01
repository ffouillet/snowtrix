jQuery(document).ready(function() {

    function showPhotoThumbForPreview($input){

        var $photoFile = $input[0].files[0];
        var $divContainer = $input.parent().parent();

        // If picture has been added.
        if(null !== $photoFile) {
            var reader = new FileReader();

            $previewImg = $($divContainer).find('img');

            // Add the img tag if not existing
            if($previewImg.length == 0) {
                $input.before('<img width="200" maxHeight="100" src="#" class="trickPhotoPreview"/>');
                $previewImg = $($divContainer).find('img');
            }

            // Hide input field.
            $input.hide();

            // Replace img src with photo preview !
            reader.onload = function(e) {
                $($previewImg).attr('src', e.target.result);
            }

            reader.readAsDataURL($photoFile);
        }
    }

    function addAddElementButtonToCollection($collectionHolder, $newLinkLi) {
        // Get the data-prototype explained earlier
        var prototype = $collectionHolder.data('prototype');

        // get the new index
        var index = $collectionHolder.data('index');

        var newForm = prototype;
        // You need this only if you didn't set 'label' => false in your tags field in TaskType
        // Replace '__name__label__' in the prototype's HTML to
        // instead be a number based on how many items we have
        // newForm = newForm.replace(/__name__label__/g, index);

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        newForm = newForm.replace(/__name__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        // Display the form in the page in an li, before the "Add a tag" link li
        var $newFormLi = $('<li></li>').append(newForm);
        $newLinkLi.before($newFormLi);

        addRemoveButtonToCollectionElement($newFormLi);

        // In case of we are adding photos, user need to preview the picture.
        if ($collectionHolder.attr('id') == "trick-photos") {
            $photoFileInput = $($newFormLi).find('input');

            $photoFileInput.on('change', function(){
                showPhotoThumbForPreview($(this));
            });
        }
    }

    function addRemoveButtonToCollectionElement($tagFormLi) {

        var $collectionHolder = $tagFormLi.parent().attr('id');
        var $removeElementButtonText = '';

        switch($collectionHolder) {
            case 'trick-photos':
                $removeElementButtonText = 'Supprimer cette photo';
                break;
            case 'trick-videos':
                $removeElementButtonText = 'Supprimer cette video';
                break;
            default :
                console.log('Unable to find collectionHolder for removeElementButtonText definition');
        }

        var $removeFormButtonHtml = '<button type="button">'+$removeElementButtonText+'</button>';
        var $removeFormButton = $($removeFormButtonHtml);

        $tagFormLi.append($removeFormButton);

        $removeFormButton.on('click', function(e) {
            // remove the li for the tag form
            $tagFormLi.remove();
        });
    }

    // Find each .trick-relation-collection and add them a "Add item" and "Remove Item" button.
    $('ul.trick-relation-collection').each(function(index){

        var $collectionHolder = $(this);
        var $collectionHolderId = $(this).attr('id');

        // Define text for add and remove elements buttons.
        switch($(this).attr('id')) {
            case 'trick-photos' :
                $addElementButtonText = "Ajouter une photo";
                $removeElementButtonText = "Supprimer cette photo";
                break;
            case 'trick-videos' :
                $addElementButtonText = "Ajouter une video";
                $removeElementButtonText = "Supprimer cette video";
                break;
            default :
                console.log('Unable to find the collection holder identifier.');
        }

        var $addElementButtonHtml = '<button type="button" class="add_'+$collectionHolderId+'_link">'+$addElementButtonText+'</button>';
        var $addElementButton = $($addElementButtonHtml);
        var $newElementLinkLiHtml = '<li class="add-'+$collectionHolderId+'"></li>';
        var $newElementLinkLi = $($newElementLinkLiHtml).append($addElementButton);

        // Add the "Add item element link li"
        $collectionHolder.append($newElementLinkLi);

        // add a delete link to all of the existing tag form li elements
            $collectionHolder.find('li:not(.add-'+$collectionHolderId+')').each(function() {
            addRemoveButtonToCollectionElement($(this));
        });

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addElementButton.on('click', function(e) {
            // add a new tag form (see next code block)
            addAddElementButtonToCollection($collectionHolder, $newElementLinkLi);
        });
    });
});
