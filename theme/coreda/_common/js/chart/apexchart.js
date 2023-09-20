var title_color = '';
var circle_color = [];
var pie_value_color = '';

if(getCookie('mode')){
  var Theme = getCookie('mode'); 
}else{
  var Theme = thisTheme;
}

if(Theme == 'white') {
    circle_color = ['#ff4500', '#ef21fd', '#6f00ff', '#0260b9','#008000'];
    title_color = '#333';
    pie_value_color = '#333';
} else if(Theme == 'dark') {
    circle_color = ['#2d7dcb', '#508a9f', '#5b6066', '#db2eb3','#266099'];
    title_color = '#fff'
    pie_value_color = '#fff';
}

function fix_value (val){
    var point = val/100;
    return Number(point.toFixed(2));
}

var options = {
series: [fix_value(chart_data.mega), fix_value(chart_data.zeta), fix_value(chart_data.zetaplus), fix_value(chart_data.super), chart_data.hash],
chart: {
    height: '400px',
    type: 'donut',
},
colors: circle_color,
labels: ['Mega', 'Zeta', 'ZetaPlus', 'Super','My'],

plotOptions: {
  pie: {
    startAngle: 0,
    endAngle: 360,
    expandOnClick: true,
    offsetX: 0,
    offsetY: 0,
    customScale: 1,
    dataLabels: {
        offset: 0,
        minAngleToShowLabel: 10
    },
    
    donut: {
        size: '60%',
        background: 'transparent',
        labels: {
            show: true,
            name: {
            show: true,
            fontSize: '24px',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontWeight: 600,
            color: '#3b86ff',
            offsetY: -10,
                formatter: function (val) {
                    return val;
                }
            },
            value: {
            show: true,
            fontSize: '16px',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontWeight: 400,
            color: pie_value_color,
            offsetY: 16,
                formatter: function (val) {
                    return val;
                }
            },
            total: {
            show: true,
            showAlways: false,
            label: 'Total',
            fontSize: '22px',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontWeight: 600,
            color: '#3b86ff',
                formatter: function (w) {
                    return w.globals.seriesTotals.reduce((a, b) => {
                     
                    return Math.round(((a + b)*100) /100)
                    }, 0)
                }
            }
        }
    } // donut end
  }
},
title: {
    text: 'My Mining Bonus Hash',
    align: 'center',
    margin: 10,
    offsetX: 0,
    offsetY: 0,
    floating: false,
    style: {
    fontSize:  '14px',
    fontWeight:  'bold',
    fontFamily:  undefined,
    color:  title_color
    },
},
fill: {
  opacity: 0.8
},
legend: {
  show: true,
  floating: false,
  fontSize: '14px',
  position: 'right',
  offsetX: 0,
  offsetY: 100,
  verticalAlign:'left',
  labels: {
    useSeriesColors: true,
  },
markers: {
    width: 12,
    height: 12,
    strokeWidth: 0,
    strokeColor: '#fff',
    fillColors: undefined,
    radius: 12,
    customHTML: undefined,
    onClick: undefined,
    offsetX: 0,
    offsetY: 0
},
itemMargin: {
    horizontal: 5,
    vertical: 0
},
onItemClick: {
    toggleDataSeries: true
},
onItemHover: {
    highlightDataSeries: true
},
formatter: function(seriesName, opts) {
    return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex] 
  },
  itemMargin: {
    vertical: 4,
    horizontal: 8,
  }
},
responsive: [{
  breakpoint: 1000,
  options: {
    chart: {
    },
    legend: {
        offsetY: 8,
        position: 'bottom'
    }
  }
}]
};