$(document).ready(function () {
    plotData1();
    var char = new Morris.Area({
        element: "chart1",
        xkey: "date1",
        ykeys: ["order1", "sales1"],
        labels: ["Order quantity", "Gross revenue"],
    });
    function plotData1() {
        $.ajax({
            url: "modules/statistical_charts/yearly_statistics.php",
            dataType: "json",
            success: function (data1) {
                char.setData(data1);
            },
        });
    }
});
$(document).ready(function () {
    plotData2();
    var char = new Morris.Line({
        element: "chart2",
        xkey: "date2",
        ykeys: ["order2", "sales2"],
        labels: ["Order quantity", "Gross revenue"],
    });
    function plotData2() {
        $.ajax({
            url: "modules/statistical_charts/monthly_statistics.php",
            dataType: "json",
            success: function (data2) {
                char.setData(data2);
            },
        });
    }
});

$(document).ready(function () {
    plotData3();
    var char = new Morris.Bar({
        element: "chart3",
        xkey: "date3",
        ykeys: ["order3", "sales3"],
        labels: ["Order quantity", "Gross revenue"],
    });
    function plotData3() {
        $.ajax({
            url: "modules/statistical_charts/weekly_statistics.php",
            dataType: "json",
            success: function (data3) {
                char.setData(data3);
            },
        });
    }
});
