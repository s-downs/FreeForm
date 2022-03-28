(function(){ jQuery(document).ready(()=>{
    let options = ['text', 'email', 'number', 'tel', 'url'];

    let formFields = jQuery("form.sdnet_freeform_form #sdnet_form_fields")
    formFields.val( formFields.val().replace(/&lquo;/g, "'") );
    formFields.val( formFields.val().replace(/^"/, '' ) );
    formFields.val( formFields.val().replace(/"$/, '' ) );
    formFields.val( formFields.val().replace(/\\+"/g, '"' ) );

    let loadOptionsFor = ( position ) => {
        let optionsDiv = jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_options .selected-options');
        let newFields = JSON.parse( formFields.val() );
        optionsDiv.html('');
        if ( position === -1 )
            return;
        let a = jQuery('<label><div>Name:</div></label>').appendTo(optionsDiv);
        let b = jQuery('<input type="text">').appendTo(a);
        b.val( newFields[position]['name'] );
        b.change(()=>{
            newFields[position]['name'] = b.val();
            formFields.val( JSON.stringify( newFields ) );
            buildFieldDiv();
        });
        a = jQuery('<label><div>Label:</div></label>').appendTo(optionsDiv);
        let c = jQuery('<input type="text">').appendTo(a);
        c.val( newFields[position]['label'] );
        c.change(()=>{
            newFields[position]['label'] = c.val();
            formFields.val( JSON.stringify( newFields ) );
            buildFieldDiv();
        });
        if ( newFields[position]['type'] === 'input' ) {
            a = jQuery('<label><div>Input Type:</div></label>').appendTo(optionsDiv);
            let d = jQuery('<select></select>').appendTo(a);
            options.forEach((opt)=>{ d.append("<option value='" + opt + "'>" + opt + "</option>")});
            d.val( newFields[position]['inputType'] );
            d.change(()=>{
                newFields[position]['inputType'] = d.val();
                formFields.val( JSON.stringify( newFields ) );
                buildFieldDiv();
            });
        }
    }
    let moveFieldUp = ( position ) => {
        let newFields = JSON.parse( formFields.val() );
        if ( position >= newFields.length )
            return;
        if ( position < 1 )
            return;
        let tmp = newFields[position];
        newFields[position] = newFields[position-1];
        newFields[position-1] = tmp;
        formFields.val( JSON.stringify( newFields ) );
        loadOptionsFor( -1 );
        buildFieldDiv();
    }
    let moveFieldDown = ( position ) => {
        let newFields = JSON.parse( formFields.val() );
        if ( position >= newFields.length - 1 )
            return;
        let tmp = newFields[position];
        newFields[position] = newFields[position+1];
        newFields[position+1] = tmp;
        formFields.val( JSON.stringify( newFields ) );
        loadOptionsFor( -1 );
        buildFieldDiv();
    }
    let removeField = ( position ) => {
        let newFields = JSON.parse( formFields.val() );
        if ( position >= newFields.length )
            return;
        newFields.splice(position, 1);
        formFields.val( JSON.stringify( newFields ) );
        loadOptionsFor( -1 );
        buildFieldDiv();
    }
    let buildFieldDiv = () => {
        let buildInput = ( info, position ) => {
            let cont = jQuery('<div class="input-component component"></div>').appendTo( container );
            cont.click(()=>{
                loadOptionsFor( position );
            });
            cont.append('<p class="normal">Text Input</p>');
            cont.append('<p class="super-small"><strong>Name: </strong>' + info['name'] + '</p>' );
            cont.append('<p class="super-small"><strong>Label: </strong>' + info['label'] + '</p>' );
            cont.append('<p class="super-small"><strong>Type: </strong>' + info['inputType'] + '</p>' );
            let icons = jQuery('<div class="icons-right"></div>').appendTo( cont );
            let moveUpIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">\n' +
                '  <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>\n' +
                '</svg></div>').appendTo( icons );
            let binIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">\n' +
                '  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>\n' +
                '</svg></div>').appendTo( icons );
            let moveDownIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">\n' +
                '  <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>\n' +
                '</svg></div>').appendTo( icons );
            moveUpIcon.click(()=>{
                moveFieldUp( position );
            });
            binIcon.click(()=>{
                removeField( position );
            });
            moveDownIcon.click(()=>{
                moveFieldDown( position );
            });
        }
        let buildTextArea = ( info, position ) => {
            let cont = jQuery('<div class="textarea-component component"></div>').appendTo( container );
            cont.click(()=>{
                loadOptionsFor( position );
            });
            cont.append('<p class="normal">Text Area</p>');
            cont.append('<p class="super-small"><strong>Name: </strong>' + info['name'] + '</p>' );
            cont.append('<p class="super-small"><strong>Label: </strong>' + info['label'] + '</p>' );
            let icons = jQuery('<div class="icons-right"></div>').appendTo( cont );
            let moveUpIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">\n' +
                '  <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>\n' +
                '</svg></div>').appendTo( icons );
            let binIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">\n' +
                '  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/>\n' +
                '</svg></div>').appendTo( icons );
            let moveDownIcon = jQuery('<div class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">\n' +
                '  <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>\n' +
                '</svg></div>').appendTo( icons );
            moveUpIcon.click(()=>{
                moveFieldUp( position );
            });
            binIcon.click(()=>{
                removeField( position );
            });
            moveDownIcon.click(()=>{
                moveFieldDown( position );
            });
        }

        let container = jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_list');
        container.html('');
        let fields = JSON.parse( formFields.val() );
        console.log( typeof fields );
        console.log( fields );
        for ( let i=0; i<fields.length; i++ ) {
            console.log(fields[i]);
            if ( fields[i]['type'] === 'input' )
                buildInput( fields[i], i );
            if ( fields[i]['type'] === 'textarea' )
                buildTextArea( fields[i], i );
        }
    };

    jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_options #add-input-type-input').click((e)=>{
        e.preventDefault();
        let optionsDiv = jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_options .selected-options');
        optionsDiv.html();
        let f = {
            type: 'input',
            name: '',
            label: '',
            inputType: 'text',
        };
        let m = jQuery('<label><div>Name:</div></label>').appendTo(optionsDiv);
        let b = jQuery('<input type="text">').appendTo(m);
        b.change(()=>{
            f['name'] = b.val();
        });
        m = jQuery('<label><div>Label:</div></label>').appendTo(optionsDiv);
        let c = jQuery('<input type="text">').appendTo(m);
        c.change(()=>{
            f['label'] = b.val();
        });
        m = jQuery('<label><div>Input Type:</div></label>').appendTo(optionsDiv);
        let d = jQuery('<select></select>').appendTo(m);
        options.forEach((opt)=>{ d.append("<option value='" + opt + "'>" + opt + "</option>")});
        d.val('text');
        d.change(()=>{
            f['inputType'] = d.val();
        });
        m = jQuery('<button class="add-button">Add</button>').appendTo(optionsDiv);
        m.click((e)=>{
            e.preventDefault();
            let fields = JSON.parse(formFields.val());
            fields[fields.length] = f;
            formFields.val(JSON.stringify(fields));
            loadOptionsFor( -1 );
            buildFieldDiv();
        });
    });
    jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_options #add-input-type-textarea').click((e)=>{
        e.preventDefault();
        let optionsDiv = jQuery('form.sdnet_freeform_form .sdnet_form_fields_ui .field_options .selected-options');
        optionsDiv.html();
        let f = {
            type: 'input',
            name: '',
            label: '',
        };
        let m = jQuery('<label><div>Name:</div></label>').appendTo(optionsDiv);
        let b = jQuery('<input type="text">').appendTo(m);
        b.change(()=>{
            f['name'] = b.val();
        });
        m = jQuery('<label><div>Label:</div></label>').appendTo(optionsDiv);
        let c = jQuery('<input type="text">').appendTo(m);
        c.change(()=>{
            f['label'] = b.val();
        });
        m = jQuery('<button class="add-button">Add</button>').appendTo(optionsDiv);
        m.click((e)=>{
            e.preventDefault();
            let fields = JSON.parse(formFields.val());
            fields[fields.length] = f;
            formFields.val(JSON.stringify(fields));
            loadOptionsFor( -1 );
            buildFieldDiv();
        });
    });

    buildFieldDiv();
    console.log( 'Yippee' );
})})();
