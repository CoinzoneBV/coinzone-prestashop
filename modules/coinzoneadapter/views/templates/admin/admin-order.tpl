<!--
 * Coinzone Adapter
 *
 * @author    Alex, 2014
 * @copyright Coinzone BV
 * @license   http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
 * @version   Release: 1.0.0
-->
<div class="panel">
    <fieldset>
        <legend><img src="{$module_dir|escape:'htmlall'}logo.gif" alt=""/> {l s='Coinzone Transaction details' mod='coinzoneadapter'}
        </legend>
        <table cellpadding="0" cellspacing="0" class="table">
            <tr>
                <td>{l s='Transaction ID' mod='coinzoneadapter'}</td>
                <td>{$transaction_details.ref_no|escape:'htmlall':'UTF-8'}</td>
            </tr>
            <tr>
                <td>{l s='Amount charged' mod='coinzoneadapter'}</td>
                <td>{$transaction_details.amount|escape:'htmlall':'UTF-8'} {$transaction_details.currency|escape:'htmlall':'UTF-8'}</td>
            </tr>
            <tr>
                <td>{l s='Date' mod='coinzoneadapter'}</td>
                <td>{$transaction_details.date_add|escape:'htmlall':'UTF-8'}</td>
            </tr>
        </table>
    </fieldset>
</div>

<div class="panel">
    <fieldset>
        <legend><img src="{$module_dir|escape:'htmlall'}logo.gif"
                     alt=""/> {l s='Proceed to a full or partial refund via Coinzone' mod='coinzoneadapter'}</legend>
        {if isset($refund) && $refund}
            <div class="conf">{l s='Refund successfully performed' mod='coinzoneadapter'}</div>
            <br/>
        {else}
            {if isset($refund) && !$refund}
                <div class="error">{l s='An error occured during this refund' mod='coinzoneadapter'}{if isset($refund_error) && $refund_error} - {$refund_error|escape:'htmlall':'UTF-8'}{/if}</div>
                <br/>
            {/if}
        {/if}
        {if $more60d}
            <div class="info">{l s='This order has been placed more than 60 days ago or no transaction details are available. Therefore, it cannot be refunded anymore.' mod='coinzoneadapter'}</div>
        {/if}
        <table class="table" cellpadding="0" cellspacing="0">
            <tr>
                <th>{l s='Date' mod='coinzoneadapter'}</th>
                <th>{l s='Amount refunded' mod='coinzoneadapter'}</th>
                <th>{l s='Reason' mod='coinzoneadapter'}</th>
                <th>{l s='Status' mod='coinzoneadapter'}</th>
            </tr>
            {assign var=total_refund value=0}
            {foreach from=$refund_details item=refund_transaction}
                <tr>
                    <td>{$refund_transaction.date_add|escape:'htmlall':'UTF-8'} </td>
                    <td>{$refund_transaction.amount|escape:'htmlall':'UTF-8'} {$refund_transaction.currency|escape:'htmlall':'UTF-8'} </td>
                    <td>{$refund_transaction.reason|escape:'htmlall':'UTF-8'} </td>
                    <td>{$refund_transaction.status|escape:'htmlall':'UTF-8'} </td>
                </tr>
                {assign var=total_refund value = $total_refund + $refund_transaction.amount}
            {/foreach}
            <tr>
                <td>{l s='Total refunded:' mod='coinzoneadapter'}</td>
                <td>{$total_refund|escape:'htmlall':'UTF-8'} {$refund_transaction.currency|escape:'htmlall':'UTF-8'} </td>
                <td>&nbsp; </td>
                <td>&nbsp; </td>
            </tr>
        </table>
        <br/>
        {if $transaction_details.amount == $total_refund && $total_refund}
            {l s='This order has been fully refunded.' mod='coinzoneadapter'}
        {else}
            <form method="post" action="" name="refund">
                {l s='Refund Amount:' mod='coinzoneadapter'} <input style="width:60px" type="text" name="refund_amount"
                                                             value="{($transaction_details.amount-$total_refund)|floatval}"/>
                {l s='Refund Reason:' mod='coinzoneadapter'} <textarea name="refund_reason" maxlength="200"></textarea>
                <br/>
                <input type="hidden" name="ref_no"
                       value="{$transaction_details.ref_no|escape:'htmlall':'UTF-8'}"/>
                <input type="submit" name="process_refund" value="{l s='Process Refund' mod='coinzoneadapter'}"
                       class="button"/>
            </form>
        {/if}
    </fieldset>
</div>