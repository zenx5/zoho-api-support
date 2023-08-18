<?php
    $client_id = get_option('zoho_api_client_id', '');
    $client_secret = get_option('zoho_api_client_secret', '');
    $organization_id = get_option('zoho_api_book_organization', '');
?>
<div style="padding:1rem">
<h1>Credentials</h1>
<div style="padding:20px 0px">
    <table>
        <tr>
            <th style="width:200px;">Organization Id: </th>
            <td>
                <input
                    type="text"
                    id="zoho_api_book_organization"
                    name="zoho_api_book_organization"
                    value="<?=$organization_id?>"/>
            </td>
        </tr>
        <tr>
            <th style="width:200px;">Zoho Client Id: </th>
            <td>
                <input
                    type="text"
                    id="zoho_api_client_id"
                    name="zoho_api_client_id"
                    value="<?=$client_id?>"/>
            </td>
        </tr>
        <tr>
            <th style="width:200px;">Zoho Client Secret: </th>
            <td>
                <input
                    type="text"
                    id="zoho_api_client_secret"
                    name="zoho_api_client_secret"
                    value="<?=$client_secret?>"/>
            </td>
        </tr>
        <tr>
            <th style="width:200px;">Zoho Code: </th>
            <td>
                <input
                    type="text"
                    id="code-access"
                    name="code-access"/>
            </td>
            <td>
                <button class="button" id="action-save-code">Generar Token</button>
            </td>
        </tr>
    </table>
    <button type="button" style="margin-top:20px; padding:5px 20px;" class="save-config button">Guardar</button>
</div>
</div>
<script>
    jQuery('.save-config')
        .click(async event => {
            const organizationId = document.querySelector('#zoho_api_book_organization').value
            const clientId = document.querySelector('#zoho_api_client_id').value
            const clientSecret = document.querySelector('#zoho_api_client_secret').value
            const response = await fetch(ajaxurl, {
                method:'post',
                headers:{
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                body:[
                    `action=update_settings`,
                    `zoho_api_book_organization=${organizationId}`,
                    `zoho_api_client_id=${clientId}`,
                    `zoho_api_client_secret=${clientSecret}`,
                ].join('&')
            })
            const result = await response.json();
            if( result ) {
                if( sessionStorage.getItem('z5_debug') ) {
                  console.log( result )
                } else {
                    document.location.reload();
                }
            }
        })
    jQuery('#action-save-code').click(async function(){
            const code = jQuery('#code-access').val();
            const response = await fetch(ajaxurl, {
                method:'post',
                headers:{
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                body:[
                    `action=generate_code`,
                    `code=${code}`
                ].join('&')
            })
            const result = await response.text();
            if( result ) {
                if( sessionStorage.getItem('z5_debug') ) {
                  console.log( result )
                } else {
                    document.location.reload();
                }
            }
        })
</script>