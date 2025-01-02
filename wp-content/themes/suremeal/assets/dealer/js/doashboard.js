Number.prototype.comma_formatter = function() {
    return this.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
}

var customTooltips = function(tooltip) {  
    var tooltipEl = document.getElementById("chartjs-tooltip");  

    if (!tooltipEl) {  
        tooltipEl = document.createElement("div");  
        tooltipEl.id = "chartjs-tooltip";  
        tooltipEl.innerHTML = "";  
        this._chart.canvas.parentNode.appendChild(tooltipEl);  
    }  

    if (tooltip.opacity === 0) {  
        tooltipEl.style.opacity = 0;  
        return;  
    }  

    if (tooltip.dataPoints && tooltip.dataPoints.length) {  
        var dataPoint = tooltip.dataPoints[0]; 

        tooltipEl.classList.remove("above", "below", "no-transform");  
        if (tooltip.yAlign) {  
            tooltipEl.classList.add(tooltip.yAlign);  
        } else {  
            tooltipEl.classList.add("no-transform");  
        }  

        var datasetIndex = dataPoint.datasetIndex;  
        var value = dataPoint.yLabel;

        var innerHtml = "<thead></thead><tbody>";  

        if (datasetIndex === 0) {  
            innerHtml += `<tr><td class="text-left text-base px-4">Revenue<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;  
        } else if (datasetIndex === 1) {  
            innerHtml += `<tr><td class="text-left text-base px-4">Order<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;  
        }  

        innerHtml += "</tbody>";  
        var tableRoot = tooltipEl.querySelector("table");  
        tableRoot.innerHTML = innerHtml;  

        var positionY = this._chart.canvas.offsetTop;  
        var positionX = this._chart.canvas.offsetLeft;  

        tooltipEl.style.opacity = 1;  
        tooltipEl.style.left = positionX + dataPoint.x + 40 + "px";
        tooltipEl.style.top = positionY + tooltip.caretY - 100 + "px";  
        tooltipEl.style.fontFamily = tooltip._bodyFontFamily;  
        tooltipEl.style.fontSize = tooltip.bodyFontSize + "px";  
        tooltipEl.style.fontStyle = tooltip._bodyFontStyle;  
        tooltipEl.style.padding = tooltip.yPadding + "px " + tooltip.xPadding + "px";  
    }  
};  

function graphClickEvent(event, array) {
    if (array[0]) {
        var chartData = array[0]["_chart"].config.data;
        var idx = array[0]["_index"];

        var label = chartData.labels[idx];
        var value = chartData.datasets[0].data[idx];
    }
}

function randomNumbers(min, max) {
    return Math.floor(Math.random() * max) + min;
}

function randomScalingFactor() {
    return randomNumbers(1, 100);
}

function createLinearGradient(ctx, color1, color2) {  
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);  
    gradient.addColorStop(0, color1);  
    gradient.addColorStop(1, color2);  
    return gradient;  
}  

let chartData = function(){
	var dates = {
	    "15m":
	        {
	            "total": 102831,
	            "upDown": 2.2,
	            "data": {
	                "labels": ["0 AM", "2 AM", "4 AM", "6 AM", "8 AM", "10 AM", "14 AM", "16AM", "18 AM", "20 AM", "22 AM"],
	                "revenue": [14000, 12000, 7000, 8000, 24000, 17000, 16000, 3813, 4607, 4194, 4753],
	                "order": [1157, 5000, 5000, 5000, 1000, 15000, 25000, 20000, 10000, 5000, 5000]
	            }
	        },
	    "1h":
	        {
	            "total": 213180,
	            "upDown": 1,
	            "data": {
	                "labels": ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
	                "revenue": [33120, 31578, 31549, 26435, 26307, 33391, 30800],
	                "order": [12254, 12947, 4417, 7137, 12364, 3339, 11704]
	            }
	        },
	    "4h":
	        {
	            "total": 3982743,
	            "upDown": 1.3,
	            "data": {
	                "labels": ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th", "11th", "12th", "13th", "14th", "15th", "16th", "17th", "18th", "19th", "20th", "21st", "22nd", "23rd", "24th", "25th", "26th", "27th", "28th", "29th", "30th"],
	                "revenue": [141010, 115138, 133009, 129413, 146080, 116199, 126854, 146997, 129636, 143285, 129345, 136018, 138606, 140743, 146910, 132684, 122024, 145827, 125049, 137724, 130477, 122255, 133749, 130146, 118660, 126728, 124164, 142161, 142976, 128876],
	                "order": [52174, 52963, 22612, 63412, 17530, 20916, 54547, 47039, 25927, 55881, 62086, 31284, 29107, 36593, 16160, 31844, 39048, 23332, 32513, 27545, 45667, 18338, 30762, 40345, 46277, 58295, 32283, 66816, 48612, 18043]
	            }
	        },
	    "1d":
	        {
	            "total": 790546,
	            "upDown": -1,
	            "data": {
	                "labels": ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
	                "revenue": [129086, 114855, 138390, 141537, 122422, 144256],
	                "order": [28399, 51685, 65043, 50953, 23260, 28851]
	            }
	        },
        "1m":
            {
                "total": 790546,
                "upDown": -1,
                "data": {
                    "labels": ["Feb", "Mar", "Apr", "May", "Jun", "Jul"],
                    "revenue": [129086, 114855, 138390, 141537, 122422, 144256],
                    "order": [28399, 51685, 65043, 50953, 23260, 28851]
                }
            }
 	};
    return {
        date: '15m',
        options: [
            {
                label: '15m',
                value: '15m',
            },
            {
                label: '1h',
                value: '1h',
            },
            {
                label: '4h',
                value: '4h',
            },
            {
                label: '1d',
                value: '1d',
            },
            {
                label: '1m',
                value: '1m',
            },
        ],
        showDropdown: false,
        selectedOption: 0,
        selectOption: function(index){
            this.selectedOption = index;
            this.date = this.options[index].value;
            this.renderChart();
        },
        data: null,
        fetch: function(){
            fetch(dates)
                .then(res => {
                    this.data = dates;
                    this.renderChart();
                })
        },
        renderChart: function(){
            let c = false;

            Chart.helpers.each(Chart.instances, function(instance) {
                if (instance.chart.canvas.id == 'chart') {
                    c = instance;
                }
            });

            if(c) {
                c.destroy();
            }

            let ctx = document.getElementById('chart').getContext('2d');
            let chart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: this.data[this.date].data.labels,
                    datasets: [
                        {
                            label: "Revenue",
                            backgroundColor: createLinearGradient(ctx, 'rgba(20, 201, 201, 0.10)', 'rgba(255, 255, 255, 0.00)'),  
                            borderColor: "#14C9C9",
                            pointStyle: 'line',
                            data: this.data[this.date].data.revenue,
                        },
                        {
                            label: "order",
                            backgroundColor: createLinearGradient(ctx, 'rgba(255,255,255,1)', 'rgba(255, 255, 255, 0.00)'),
                            borderColor: "#18A0FB",
                            pointStyle: 'line',
                            pointBackgroundColor: "#fff",
                            data: this.data[this.date].data.order,
                        },
                    ],
                },
                options: {
                	legend: {
			            display: false,
			        },
			        tooltips: {
			            // mode: "index",
			            // intersect: true,
			            // enabled: false,
			            // mode: "index",
			            // position: "average",

			            // custom: customTooltips,
			            // callbacks: {
			            //     label: function(tooltipItem, data) {
			            //         var label =
			            //             data.datasets[tooltipItem.datasetIndex].label || "";

			            //         if (label) {
			            //             label += "";
			            //         }
			            //         return "$"+tooltipItem.yLabel;
			            //     }
			            // }

                        enabled: false,  
                        custom: customTooltips,
			        },
                    scales: {
                        // xAxes: [{
                        //     gridLines: {
                        //         display: false
                        //     },
                        //     ticks: {
                        //     	fontSize: 16,
                        //     	fontFamily: 'Poppins',
                        //         callback: function(value,index,array) {
                        //             return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
                        //         }
                        //     }
                        // }],
                        // yAxes: [
                        // 	{
                        // 		ticks: {
                        // 			fontSize: 16,
                        //     		fontFamily: 'Poppins',
                        //             callback: function(value,index,array) {
                        //                 return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
                        //             }
                        // 		}

                        // 	}
                        // ]
                        xAxes: [{ 
                            gridLines: { display: false },
                            ticks: {
                                fontSize: 16,
                                fontFamily: 'Poppins',
                                callback: function(value,index,array) {
                                    return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
                                }
                            }
                        }],  
                        yAxes: [{ 
                            ticks: { 
                                beginAtZero: true,
                                fontSize: 16,
                                fontFamily: 'Poppins',
                                callback: function(value,index,array) {
                                    return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
                                }
                            } 
                        }],  
                    },
                }
            });
        }
    }
}