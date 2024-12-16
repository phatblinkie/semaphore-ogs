<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <div>
    <v-toolbar flat>
      <v-toolbar-title> System Status </v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">
        System Status
      </v-tab>
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Task Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus"
             :to="`/project/${projectId}/compliancestatus`">
        Compliance Status
      </v-tab>
    </v-tabs>

    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="20"
      :options.sync="tableOptions"
      class="mt-4"
    >
      <template v-slot:item.hostname="{ item }">
        <router-link :to="`/project/${projectId}/host/${item.hostname}`"
        >{{ item.hostname }}</router-link>
      </template>
      <template v-slot:item.ansible_ping="{ item }">
        <TaskStatus :status="item.ansible_ping"/>
      </template>
      <template v-slot:item.disk_capacity="{ item }">
        <div class="disk-meter">
          <meter
            :value="item.disk_capacity"
            :min="0"
            :max="100"
            :low="70"
            :high="90"
            :optimum="0">
          </meter>
          <span>{{ item.disk_capacity }}%</span>
        </div>
      </template>
      <template v-slot:item.proc_usage="{ item }">
        <div class="proc-meter">
          <meter
            :value="roundProcUsage(item.proc_usage)"
            :min="0"
            :max="100"
            :low="70"
            :high="90"
            :optimum="0">
          </meter>
          <span>{{ roundProcUsage(item.proc_usage) }}%</span>
        </div>
      </template>
      <template v-slot:item.app_check="{ item }">
        <AppCheckStatus :status="item.app_check"/>
      </template>
      <template v-slot:item.last_responded="{ item }">
        <span>{{ formatTimestamp(item.last_responded) }}</span>
      </template>
      <template v-slot:item.uptime="{ item }">
        <span>{{ formatUptime(item.uptime) }}</span>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from 'axios';
import TaskStatus from '@/components/TaskStatus.vue';
import AppCheckStatus from '@/components/AppCheckStatus.vue';

export default {
  components: {
    TaskStatus,
    AppCheckStatus,
  },
  data() {
    return {
      projectId: this.$route.params.projectId, // Extract projectId from route parameters
      headers: [
        { text: 'Hostname', value: 'hostname' },
        { text: 'Ansible Ping', value: 'ansible_ping' },
        { text: 'Disk Usage', value: 'disk_capacity' },
        { text: 'Proc Usage', value: 'proc_usage' },
        { text: 'App Check', value: 'app_check' },
        { text: 'Last Responded', value: 'last_responded' },
        { text: 'Uptime', value: 'uptime' },
      ],
      items: [], // This will be populated with data from the JSON source
      tableOptions: {
        sortBy: ['hostname'],
        sortDesc: [false],
      },
    };
  },
  mounted() {
    this.fetchData();
    this.interval = setInterval(this.fetchData, 15000); // Refresh data every 15 seconds
  },
  beforeDestroy() {
    clearInterval(this.interval); // Clear the interval when the component is destroyed
  },
  methods: {
    fetchData() {
      axios
        .get(`/post/get_system_status.php?project_id=${this.projectId}`) // Use projectId to fetch data
        .then((response) => {
          this.items = Array.isArray(response.data) ? response.data : [];
          // Ensure items is always an array
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.items = []; // Set items to an empty array on error
        });
    },
    roundProcUsage(value) {
      return Math.round(value);
    },
    formatTimestamp(timestamp) {
      if (!timestamp) return 'N/A'; // Return 'N/A' if timestamp is undefined or null
      const date = new Date(timestamp.replace(' ', 'T')); // Replace space with 'T' to make it ISO 8601 compliant
      return date.toLocaleString(); // Format the date as a local string
    },
    formatUptime(uptime) {
      if (uptime === null) return 'N/A'; // Return 'N/A' if uptime is null
      const seconds = uptime % 60;
      const minutes = Math.floor((uptime % 3600) / 60);
      const hours = Math.floor(uptime / 3600);
      return `${hours}h ${minutes}m ${seconds}s`;
    },
  },
};
</script>

<style scoped>
.chart-wrapper {
  display: flex;
  align-items: center;
  justify-content: left;
}
.left-aligned-table {
  text-align: left;
}
.left-aligned-table th,
.left-aligned-table td {
  text-align: left; /* Align text to the left in table headers and cells */
  padding: 8px; /* Optional: add padding to table cells for better spacing */
}
.disk-meter, .proc-meter {
  display: flex;
  align-items: center;
}
.disk-meter meter, .proc-meter meter {
  width: 100%;
  height: 20px;
  margin-right: 8px;
}
</style>
