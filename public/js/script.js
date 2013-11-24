$(document).ready(function() {
    var ctx = $("#httpsUsageChart").get(0).getContext("2d");
    var httpsUsageChart = new Chart('en', ctx).Pie(httpsUsageData);


    var ctx2 = $("#hstsUsageChart").get(0).getContext("2d");
    var hstsUsageChart = new Chart('en', ctx2).Pie(hstsUsageData);

    var ctx2 = $("#maxAgeUsageChart").get(0).getContext("2d");
    var maxAgeUsageChart = new Chart('en', ctx2).Pie(maxAgeUsageData);
});