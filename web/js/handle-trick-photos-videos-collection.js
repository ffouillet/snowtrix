jQuery(document).ready(function() {

    function validateTrickPhotoFileExtension($photoFile) {
        var acceptedFileTypes = ['jpg','jpeg','png'];
        var successOrError = {'success' : false, 'errorMessage' : ''};

        var fileExtension = $photoFile.name.split('.').pop().toLowerCase();

        // Check file extension
        if(acceptedFileTypes.indexOf(fileExtension) < 0) {
            successOrError.errorMessage = "Impossible d'ajouter cette photo car le format est incorrect. Format d'images supportés : .jpg, .png";
            return successOrError;
        }

        successOrError.success = true;

        return successOrError;
    }

    function validateTrickPhotoSize($trickPhoto){
        var requiredSize = {'minWidth' : 150, 'maxWidth' : 1920, 'minHeight' : 150, 'maxHeight' : 1080 };

        var successOrError = {'success' : false, 'errorMessage' :''}

        if($trickPhoto.width < requiredSize.minWidth) {
            successOrError.errorMessage =
                "Impossible d'ajouter cette photo car sa largeur est trop petite (Largeur minimum de photo requise : "+requiredSize.minWidth+"px).";
        } else if($trickPhoto.width > requiredSize.maxWidth) {
            successOrError.errorMessage =
                "Impossible d'ajouter cette photo car sa largeur est trop grande (Largeur maximum de photo autorisée : "+requiredSize.maxWidth+"px).";
        } else if($trickPhoto.height < requiredSize.minHeight) {
            successOrError.errorMessage =
                "Impossible d'ajouter cette photo car sa hauteur est trop petite (Hauteur minimum de photo requise : "+requiredSize.minHeight+"px).";
        } else if($trickPhoto.height > requiredSize.maxHeight) {
            successOrError.errorMessage =
                "Impossible d'ajouter cette photo car sa hauteur est trop petite (Hauteur maximum de photo autorisée : "+requiredSize.maxHeight+"px).";
        } else {
            successOrError.success = true;
        }

        return successOrError;
    }

    function showPhotoThumbForPreview($input){

        var $photoFile = $input[0].files[0];
        var $currentLiItem = $input.parent(); // Will be used to add img and set it src.

        // Check if image extension is valid.
        var imageExtensionValid = validateTrickPhotoFileExtension($photoFile);

        if(imageExtensionValid.success == false) {
            alert(imageExtensionValid.errorMessage);
            $input.val('');
            return;
        }

        // If picture has been added.
        if(null !== $photoFile) {
            var reader = new FileReader();

            // Replace img src with photo preview !
            reader.onload = function(e) {

                var trickPhoto = new Image();
                trickPhoto.src = e.target.result;
                trickPhoto.onload = function(){

                    var $trickPhotoSizeValid = validateTrickPhotoSize(trickPhoto);

                    if($trickPhotoSizeValid.success == false) {
                        alert($trickPhotoSizeValid.errorMessage);
                        $input.val('');
                        return;
                    } else {
                        $previewImg = $($currentLiItem).find('img');

                        // Add the img tag if not existing
                        if($previewImg.length == 0) {
                            $input.before('<img width="200" maxHeight="100" src="#" class="trickPhotoPreview"/>');
                            $previewImg = $($currentLiItem).find('img');
                        }

                        $($previewImg).attr('src', e.target.result);

                    }
                };
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
        var newLinkLiClass = $($collectionHolder).attr('id') + '-form-element';

        var $newFormLi = $('<li class="'+newLinkLiClass+'"></li>').append(newForm);
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

        var $removeFormButtonHtml = '<button type="button" class="remove-element-button"><i class="fa fa-trash"></i>'+$removeElementButtonText+'</button>';
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

        var $addElementButtonHtml = '<button type="button" class="add_'+$collectionHolderId+'_link"><i class="fa fa-plus-circle" aria-hidden="true"></i>'+$addElementButtonText+'</button>';
        var $addElementButton = $($addElementButtonHtml);
        var $newElementLinkLiHtml = '<li class="add-'+$collectionHolderId+'"></li>';
        var $newElementLinkLi = $($newElementLinkLiHtml).append($addElementButton);

        // Add the "Add item element link li"
        $collectionHolder.append($newElementLinkLi);

        // add a delete link to all of the existing tag form li elements
        $collectionHolder.find('li:not(.add-'+$collectionHolderId+')').each(function() {
            // Do not add delete button to form errors.
            if($(this).parent().parent().parent().attr('class') != 'form-error') {
                addRemoveButtonToCollectionElement($(this));
            }
        });

        // count the current form inputs we have (e.g. 2), use that as the new
        // index when inserting a new item (e.g. 2)
        $collectionHolder.data('index', $collectionHolder.find(':input').length);

        $addElementButton.on('click', function(e) {
            // add a new tag form (see next code block)
            addAddElementButtonToCollection($collectionHolder, $newElementLinkLi);
        });

        // Add possibility for exising trick photos input to have photo change if required
        // In case of we are adding photos, user need to preview the picture.
        if ($collectionHolder.attr('id') == "trick-photos") {
            $photoFileInputs = $($collectionHolder).find('input');

            $photoFileInputs.each(function(){
                $(this).on('change', function(){
                    showPhotoThumbForPreview($(this));
                });
            });
        }
    });
});
