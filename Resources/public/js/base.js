/*
This file is part of the CampaignChain package.

(c) CampaignChain, Inc. <info@campaignchain.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
*/

/**
 *  Define the CampaignChainHookDateRepeat class.
 *
 * @constructor
 */
function CampaignChainHookDateRepeat(){

    // Frequency sub-forms
    this.daily = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_daily").closest('.form-group');
    this.weekly = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_weekly").closest('.form-group');
    this.monthly = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_monthly").closest('.form-group');
    this.yearly = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_yearly").closest('.form-group');

    // Fields of monthly frequency
    this.monthly_repeat_by = $('input[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][monthly][repeat_by]"]');
    this.monthly_repeat_by_dom = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_monthly_repeat_by_0");
    this.monthly_repeat_by_dow = $("#campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_monthly_repeat_by_1");
    this.monthly_dom_occurrence = $('select[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][monthly][dom_occurrence]"]');
    this.monthly_dom_occurrence = $('select[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][monthly][dom_occurrence]"]');
    this.monthly_dom_occurrence_select2 = $("#s2id_campaignchain_core_campaign_campaignchain_hook_campaignchain_date_repeat_monthly_dom_occurrence");
    this.monthly_dow_occurrence = $('input[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][monthly][dow_occurrence]"]');
    this.monthly_day_of_week = $('input[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][monthly][day_of_week]"]');

    // Fields of ends sub-form
    this.ends_occurrences = $('input[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][ends][occurrences]"]');
    this.ends_end_date = $('input[name="campaignchain_core_campaign[campaignchain_hook_campaignchain_date_repeat][ends][interval_end_date]"]');
}

CampaignChainHookDateRepeat.prototype.switchFrequency = function(frequency)
{
    switch(frequency) {
        case 'daily':
            this.daily.show();
            this.weekly.hide();
            this.monthly.hide();
            this.monthly_repeat_by.attr('required', false);
            this.yearly.hide();
            break;
        case 'weekly':
            this.daily.hide();
            this.weekly.show();
            this.monthly.hide();
            this.monthly_repeat_by.attr('required', false);
            this.yearly.hide();
            break;
        case 'monthly':
            this.daily.hide();
            this.weekly.hide();
            this.monthly.show();
            this.monthly_repeat_by.attr('required', true);
            this.yearly.hide();
            break;
        case 'yearly':
            this.daily.hide();
            this.weekly.hide();
            this.monthly.hide();
            this.monthly_repeat_by.attr('required', false);
            this.yearly.show();
            break;
        default:
            this.weekly.hide();
            this.monthly.hide();
            this.yearly.hide();
            break;
    }
}

CampaignChainHookDateRepeat.prototype.switchMonthly = function(repeat_by)
{
    switch(repeat_by) {
        case 'day_of_month':
            this.monthly_dom_occurrence.attr("disabled", false);
            this.monthly_dom_occurrence.attr("required", true);
            this.monthly_dow_occurrence.attr("disabled", true);
            this.monthly_dow_occurrence.attr('checked', false);
            this.monthly_day_of_week.attr("disabled", true);
            this.monthly_day_of_week.attr('checked', false);
            break;
        case 'day_of_week':
            this.monthly_dom_occurrence.attr("disabled", true);
            this.monthly_dom_occurrence_select2.select2("val", "");
            this.monthly_dow_occurrence.attr("disabled", false);
            this.monthly_dow_occurrence.attr("required", true);
            this.monthly_day_of_week.attr("disabled", false);
            this.monthly_day_of_week.attr("required", true);
            break;
        default:
            // Initial state is that all sub-items of a radio button are disabled.
            this.monthly_dom_occurrence.attr("disabled", true);
            this.monthly_dow_occurrence.attr("disabled", true);
            this.monthly_day_of_week.attr("disabled", true);
            break;
    }
}

CampaignChainHookDateRepeat.prototype.switchEnd = function(end)
{
    switch(end) {
        case 'never':
            this.ends_occurrences.attr("disabled", true);
            this.ends_occurrences.val('');
            this.ends_end_date.attr("disabled", true);
            this.ends_end_date.val('');
            break;
        case 'occurrences':
            this.ends_occurrences.attr("disabled", false);
            this.ends_occurrences.attr("required", true);
            this.ends_end_date.attr("disabled", true);
            this.ends_end_date.val('');
            break;
        case 'date':
            this.ends_occurrences.attr("disabled", true);
            this.ends_occurrences.val('');
            this.ends_end_date.attr("disabled", false);
            this.ends_end_date.attr("required", true);
            break;
        default:
            // Initial state is that all sub-items of a radio button are disabled.
            this.ends_occurrences.attr("disabled", true);
            this.ends_end_date.attr("disabled", true);
            break;
    }
}