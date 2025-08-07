{if $auth.user_id}
    {capture name="competitive_map_popup"}
        <div title="{__("competitive_map_download")}" id="competitive_map_popup">
            <p>
                {__("competitive_map_popup_descr")}
            </p>
            <div class="buttons-container">
                <button class="cm-dialog-closer ty-btn ty-btn__primary">{__("cancel")}</button>
                <a class="ty-btn ty-btn__secondary ty-float-right" href="{"competitive_map.download?category_id=`$category_data.category_id`"|fn_url}">{__("download")}</a>
            </div>
        </div>
    {/capture}

    {include file="common/popupbox.tpl"
    link_text="{__("competitive_map_download")}"
    title="{__("competitive_map_download")}"
    id="request_dialog_competitive_map_popup_{$category_data.category_id}"
    content=$smarty.capture.competitive_map_popup
    link_meta="ty-btn__primary ty-btn__big ty-btn ty-float-right"
    }
{/if}
