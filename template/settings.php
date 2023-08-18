<?php
    $zoho_api_books = get_option("zoho_api_books");
    $access_token = get_option( 'zoho_api_access_token', '' );
?>
<style>
    th, td {
        width:10rem;
        border: 1px solid lightgray;
        padding: 0.6rem 0.8rem;
        text-align: center;
    }
    .button {
        width:100%;
        padding: 0.8rem !important;
        font-weight: bold;
    }
    .container-button {
        padding: 0;
        padding-top: 0.7rem;
        border: none;
    }
</style>

<div style="padding:1rem">
<h1>Settings</h1>
<h2>Status</h2>
<div><?php
    if($access_token!='') {
        echo "<script>";
        // echo "console.log(".json_encode( ZohoBooks::list_all_contacts() ).")";
        echo "</script>";
        echo "<div style='display:inline-block; font-weight:bold;top:10px; padding:4px 20px; color:white; background-color:green; margin:5px; border-radius:20px;'>Active</div>";
    } else {
        echo "<div style='display:inline-block; font-weight:bold;top:10px; padding:4px 20px; color:white; background-color:red; margin:5px; border-radius:20px;'>Inactive token</div>";
    }
?></div>
<h2>Enable APIs</h2>
<table>
    <tr>
        <th>Api Name</th>
        <th>Status</th>
    </tr>
    <tr>
        <td>Books</td>
        <td>
            <input type="checkbox" name="zoho_api_books" <?=$zoho_api_books ? "checked" : ""?>/>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="container-button">
            <button class="button" type="button" id="update_books_api">Guardar</button>
        </td>
    </tr>
</table>
<script>
    jQuery("#update_books_api")
        .click(function(){
            const books = jQuery("input[name='zoho_api_books']").is(":checked") ? "1" : "0"
            const response = fetch(ajaxurl, {
                method:'post',
                headers:{
                    'Content-Type':'application/x-www-form-urlencoded'
                },
                body: `action=update_settings&zoho_api_books=${books}`,
            }).then( response => document.location.reload() )
        })
</script>