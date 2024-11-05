<template>
  <div>
    <div class="chart-wrapper">
      <apexchart
        width="800" type="bar"
        :options="graph1options" :series="graph1series">
      </apexchart>
    </div>
    <hr>
    <div class="chart-wrapper">
      <apexchart
        type="bar" width="800"
        :options="stackedBarChartOptions" :series="barseries">
      </apexchart>
    </div>
    <hr>
    <div class="chart-wrapper">
      <apexchart
        width="800" type="line"
        :options="linechartoptions" :series="lineseries">
      </apexchart>
    </div>
    <hr>
    <div class="chart-wrapper">
      <apexchart
        width="600" type="donut"
        :options="donutOptions" :series="donutSeries">
      </apexchart>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SystemStatus',
  data() {
    return {
      graph1options: {
        chart: {
          id: 'vuechart-example1',
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        title: {
          text: 'Ansible Task Success Rates',
          align: 'center',
          style: {
            fontSize: '20px',
          },
        },
        colors: ['#00897b', '#DC143C'],
      },
      graph1series: [
        {
          name: 'Success',
          data: [55, 62, 89, 66, 98, 72, 101, 75, 94, 120, 117, 139],
        },
        {
          name: 'Failure',
          data: [5, 6, 9, 22, 0, 7, 15, 25, 4, 1, 0, 21],
        },
      ],

      stackedBarChartOptions: {
        chart: {
          id: 'stacked-bar-chart',
          type: 'bar',
          stacked: true,
          height: 350,
          toolbar: {
            show: true,
          },
          zoom: {
            enabled: true,
          },
        },
        xaxis: {
          type: 'datetime',
          categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT', '01/05/2011 GMT', '01/06/2011 GMT'],
        },
        plotOptions: {
          bar: {
            horizontal: false,
            borderRadius: 10,
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'last',
          },
        },
        legend: {
          position: 'right',
          offsetY: 40,
        },
        fill: {
          opacity: 1,
        },
      },
      barseries: [
        {
          name: 'PRODUCT A',
          data: [44, 55, 41, 67, 22, 43],
        },
        {
          name: 'PRODUCT B',
          data: [13, 23, 20, 8, 13, 27],
        },
        {
          name: 'PRODUCT C',
          data: [11, 17, 15, 15, 21, 14],
        },
        {
          name: 'PRODUCT D',
          data: [21, 7, 25, 13, 22, 8],
        },
      ],

      linechartoptions: {
        chart: {
          id: 'vuechart-example2',
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        title: {
          text: 'Ansible Task Success Rates',
          align: 'center',
          style: {
            fontSize: '20px',
          },
        },
        colors: ['#00897b', '#DC143C'],
      },
      lineseries: [
        {
          name: 'PRODUCT A',
          data: [44, 55, 41, 67, 22, 43],
        },
        {
          name: 'PRODUCT B',
          data: [13, 23, 20, 8, 13, 27],
        },
        {
          name: 'PRODUCT C',
          data: [11, 17, 15, 15, 21, 14],
        },
        {
          name: 'PRODUCT D',
          data: [21, 7, 25, 13, 22, 8],
        },
      ],

      donutOptions: {
        labels: ['Redhat 9', 'Windows 10', 'Windows Server 2019', 'Cisco', 'Unknown'],
        colors: ['#FF4560', '#008FFB', '#00E396', '#775DD0', '#FEB019'],
        title: {
          text: 'OGS Operating Systems',
          align: 'center',
          style: {
            fontSize: '20px',
          },
        },
      },
      donutSeries: [44, 55, 41, 17, 15],
    };
  },
};
</script>

<style scoped>
.chart-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
