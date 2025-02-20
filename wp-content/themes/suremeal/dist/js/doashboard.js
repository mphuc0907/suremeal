// Number.prototype.comma_formatter = function() {
//     return this.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
// }
//
// var customTooltips = function(tooltip) {
//     var tooltipEl = document.getElementById("chartjs-tooltip");
//
//     if (!tooltipEl) {
//         tooltipEl = document.createElement("div");
//         tooltipEl.id = "chartjs-tooltip";
//         tooltipEl.innerHTML = "";
//         this._chart.canvas.parentNode.appendChild(tooltipEl);
//     }
//
//     if (tooltip.opacity === 0) {
//         tooltipEl.style.opacity = 0;
//         return;
//     }
//
//     if (tooltip.dataPoints && tooltip.dataPoints.length) {
//         var dataPoint = tooltip.dataPoints[0];
//
//         tooltipEl.classList.remove("above", "below", "no-transform");
//         if (tooltip.yAlign) {
//             tooltipEl.classList.add(tooltip.yAlign);
//         } else {
//             tooltipEl.classList.add("no-transform");
//         }
//
//         var datasetIndex = dataPoint.datasetIndex;
//         var value = dataPoint.yLabel;
//
//         var innerHtml = "<thead></thead><tbody>";
//
//         if (datasetIndex === 0) {
//             innerHtml += `<tr><td class="text-left text-base px-4">Revenue<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;
//         } else if (datasetIndex === 1) {
//             innerHtml += `<tr><td class="text-left text-base px-4">Order<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;
//         }
//
//         innerHtml += "</tbody>";
//         var tableRoot = tooltipEl.querySelector("table");
//         tableRoot.innerHTML = innerHtml;
//
//         var positionY = this._chart.canvas.offsetTop;
//         var positionX = this._chart.canvas.offsetLeft;
//
//         tooltipEl.style.opacity = 1;
//         tooltipEl.style.left = positionX + dataPoint.x + 40 + "px";
//         tooltipEl.style.top = positionY + tooltip.caretY - 100 + "px";
//         tooltipEl.style.fontFamily = tooltip._bodyFontFamily;
//         tooltipEl.style.fontSize = tooltip.bodyFontSize + "px";
//         tooltipEl.style.fontStyle = tooltip._bodyFontStyle;
//         tooltipEl.style.padding = tooltip.yPadding + "px " + tooltip.xPadding + "px";
//     }
// };
//
// function graphClickEvent(event, array) {
//     if (array[0]) {
//         var chartData = array[0]["_chart"].config.data;
//         var idx = array[0]["_index"];
//
//         var label = chartData.labels[idx];
//         var value = chartData.datasets[0].data[idx];
//     }
// }
//
// function randomNumbers(min, max) {
//     return Math.floor(Math.random() * max) + min;
// }
//
// function randomScalingFactor() {
//     return randomNumbers(1, 100);
// }
//
// function createLinearGradient(ctx, color1, color2) {
//     const gradient = ctx.createLinearGradient(0, 0, 0, 400);
//     gradient.addColorStop(0, color1);
//     gradient.addColorStop(1, color2);
//     return gradient;
// }
//
// let chartData = function(){
//     var dates = {
//         "15m":
//             {
//                 "total": 102831,
//                 "upDown": 2.2,
//                 "data": {
//                     "labels": ["0 AM", "2 AM", "4 AM", "6 AM", "8 AM", "10 AM", "14 AM", "16AM", "18 AM", "20 AM", "22 AM"],
//                     "revenue": [14000, 12000, 7000, 8000, 24000, 17000, 16000, 3813, 4607, 4194, 4753],
//                     "order": [1157, 5000, 5000, 5000, 1000, 15000, 25000, 20000, 10000, 5000, 5000]
//                 }
//             },
//         "1h":
//             {
//                 "total": 213180,
//                 "upDown": 1,
//                 "data": {
//                     "labels": ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
//                     "revenue": [33120, 31578, 31549, 26435, 26307, 33391, 30800],
//                     "order": [12254, 12947, 4417, 7137, 12364, 3339, 11704]
//                 }
//             },
//         "d":
//             {
//                 "total": 3982743,
//                 "upDown": 1.3,
//                 "data": {
//                     "labels": ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th", "13th", "14th", "15th", "16th", "17th", "18th", "19th", "20th", "21st", "22nd", "23rd", "24th", "25th", "26th", "27th", "28th", "29th", "30th"],
//                     "revenue": [1600, 500, 411, 550, 1200, 1600, 1500, 800, 900, 400, 600, 1200, 1600, 1100, 1400, 500, 200, 100, 200, 1600, 1500, 1600, 1000, 1100, 1100, 1000, 1400, 300, 200, 1500],
//                     "order": [1, 2, 3, 7, 1, 2, 40, 12, 23, 12, 2, 6, 43, 2, 87, 6, 33, 12, 12, 2, 3, 31, 12, 14, 2, 10, 3, 32, 8, 32]
//                 }
//             },
//         "m":
//             {
//                 "total": 790546,
//                 "upDown": -1,
//                 "data": {
//                     "labels": ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
//                     "revenue": [129086, 114855, 138390, 141537, 122422, 144256],
//                     "order": [28399, 51685, 65043, 50953, 23260, 28851]
//                 }
//             },
//         "y":
//             {
//                 "total": 790546,
//                 "upDown": -1,
//                 "data": {
//                     "labels": ["2020", "2021", "2022", "2023", "2024", "2025"],
//                     "revenue": [129086, 114855, 138390, 141537, 122422, 144256],
//                     "order": [28399, 51685, 65043, 50953, 23260, 28851]
//                 }
//             }
//     };
//     return {
//         date: 'd',
//         options: [
//             {
//                 label: '15m',
//                 value: '15m',
//             },
//             {
//                 label: '1h',
//                 value: '1h',
//             },
//             {
//                 label: 'd',
//                 value: 'd',
//             },
//             {
//                 label: 'm',
//                 value: 'm',
//             },
//             {
//                 label: 'y',
//                 value: 'y',
//             },
//         ],
//         showDropdown: false,
//         selectedOption: 0,
//         selectOption: function(index){
//             this.selectedOption = index;
//             this.date = this.options[index].value;
//             this.renderChart();
//         },
//         data: null,
//         fetch: function(){
//             fetch(dates)
//                 .then(res => {
//                     this.data = dates;
//                     this.renderChart();
//                 })
//         },
//         renderChart: function(){
//             let c = false;
//
//             Chart.helpers.each(Chart.instances, function(instance) {
//                 if (instance.chart.canvas.id == 'chart') {
//                     c = instance;
//                 }
//             });
//
//             if(c) {
//                 c.destroy();
//             }
//
//             let ctx = document.getElementById('chart').getContext('2d');
//             let chart = new Chart(ctx, {
//                 type: "line",
//                 data: {
//                     labels: this.data[this.date].data.labels,
//                     datasets: [
//                         {
//                             label: "Revenue",
//                             backgroundColor: createLinearGradient(ctx, 'rgba(20, 201, 201, 0.10)', 'rgba(255, 255, 255, 0.00)'),
//                             borderColor: "#14C9C9",
//                             pointStyle: 'line',
//                             data: this.data[this.date].data.revenue,
//                             yAxisID: "left"
//                         },
//                         {
//                             label: "order",
//                             backgroundColor: createLinearGradient(ctx, 'rgba(255,255,255,1)', 'rgba(255, 255, 255, 0.00)'),
//                             borderColor: "#18A0FB",
//                             pointStyle: 'line',
//                             pointBackgroundColor: "#fff",
//                             data: this.data[this.date].data.order,
//                             yAxisID: "right"
//                         },
//                     ],
//                 },
//                 options: {
//                     legend: {
//                         display: false,
//                     },
//                     tooltips: {
//                         enabled: false,
//                         custom: customTooltips,
//                     },
//                     scales: {
//                         xAxes: [{
//                             gridLines: { display: false },
//                             ticks: {
//                                 fontSize: 16,
//                                 fontFamily: 'Poppins',
//                                 callback: function(value,index,array) {
//                                     return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
//                                 }
//                             }
//                         }],
//                         yAxes: [
//                             {
//                                 "scaleLabel": {
//                                     "display": true,
//                                 },
//                                 "id": "left",
//                                 "stacked": false,
//                                 "ticks": {
//                                     "beginAtZero": true,
//                                     suggestedMin: 0,
//                                     suggestedMax: 1600,
//                                     callback: function(value) {
//                                         return value + ' USD';
//                                     }
//                                 }
//                             },
//                             {
//                                 "scaleLabel": {
//                                     "display": true,
//                                 },
//                                 "id": "right",
//                                 "position": "right",
//                                 "stacked": false,
//                                 "ticks": {
//                                     "beginAtZero": true,
//                                     suggestedMin: 5,
//                                     suggestedMax: 40
//                                 }
//                             }
//                         ]
//                     },
//                 }
//             });
//         }
//     }
// }