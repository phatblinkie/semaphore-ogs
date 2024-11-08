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
        type="bar" width="800" ref="stackedBarChartOptions"
        :options="stackedBarChartOptions" :series="stackedseries">
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
import axios from 'axios';

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
      graph1series: [],

      stackedBarChartOptions: {
        chart: {
          id: 'stacked-bar-chart',
          type: 'bar',
          stacked: true,
          stackType: '100%',
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
          categories: [],
          // Initial empty array for categories
          tickPlacement: 'between',
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
          position: 'top',
        },
        fill: {
          opacity: 1,
        },
      },
      stackedseries: [],

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
      lineseries: [],

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
      donutSeries: [],
    };
  },
  mounted() {
    this.fetchGraph1Series();
    this.fetchBarSeries();
    this.fetchLineSeries();
    this.fetchDonutSeries();
  },
  methods: {
    async fetchGraph1Series() {
      try {
        const response = await axios.get('http://192.168.32.133/systemstatus/graph-bar.php');
        this.graph1series = response.data;
      } catch (error) {
        console.error('Error fetching graph1series data:', error);
      }
    },
    async fetchBarSeries() {
      try {
        const response = await axios.get('http://192.168.32.133/post/get_7_date_task_results.php');
        const data = response.data;

        // Log the fetched data to verify it
        console.log('Fetched Bar Series Data:', data);

        // Ensure categories and series data are properly assigned
        if (data && data.dates && data.series) {
          this.stackedBarChartOptions.xaxis.categories = data.dates;
          // Ensure categories are set correctly
          this.stackedseries = data.series;

          // Log the updated chart options to verify them
          console.log('Updated Xaxis Categories:', this.stackedBarChartOptions.xaxis.categories);
          console.log('Updated Series Data:', this.stackedseries);
          // const newCategories = ['Positive', 'Neutral', 'Negative'];
          // newCategories.forEach((category) => {
          //   this.stackedBarChartOptions.xaxis.categories.push(category);
          // });
          // this.series[0].data = [yourData];
          // this.options.xaxis.categories = [yourCategories]
          this.$refs.stackedBarChartOptions.refresh();
          console.log('Updated Xaxis Categories:', this.stackedBarChartOptions.xaxis.categories);
        } else {
          console.error('Invalid data format:', data);
        }
      } catch (error) {
        console.error('Error fetching chart data:', error);
      }
    },
    async fetchLineSeries() {
      try {
        const response = await axios.get('http://192.168.32.133/systemstatus/graph-line.php');
        this.lineseries = response.data;
      } catch (error) {
        console.error('Error fetching lineseries data:', error);
      }
    },
    async fetchDonutSeries() {
      try {
        const response = await axios.get('http://192.168.32.133/systemstatus/graph-donut.php');
        this.donutSeries = response.data;
      } catch (error) {
        console.error('Error fetching donutSeries data:', error);
      }
    },
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
