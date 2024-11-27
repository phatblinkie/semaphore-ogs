<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <div>
    <v-toolbar flat>
      <v-toolbar-title> System Status </v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">System Status</v-tab>
      <v-tab key="Graphs" :to="`/project/${projectId}/graphs`">Graphs</v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
      <v-tab key="Compliancestatus"
      :to="`/project/${projectId}/compliancestatus`">Compliance Status</v-tab>
    </v-tabs>

    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="20"
      class="mt-4"
    >
      <template v-slot:item.ansible_ping="{ item }">
        <TaskStatus :status="item.ansible_ping"/>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from 'axios';
import TaskStatus from '@/components/TaskStatus.vue';

export default {
  components: {
    TaskStatus,
  },
  data() {
    return {
      projectId: 1, // Replace this with actual project ID extraction logic if needed
      headers: [
        { text: 'Hostname', value: 'hostname' },
        { text: 'Ansible Ping', value: 'ansible_ping' },
        { text: 'Disk Usage', value: 'disk_capacity' },
        { text: 'Proc Usage', value: 'proc_usage' },
        { text: 'App Check', value: 'app_check' },
      ],
      items: [], // This will be populated with data from the JSON source
    };
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      axios
        .get('/post/get_system_status.php') // Replace with your actual JSON source URL
        .then((response) => {
          this.items = response.data; // Assuming the JSON data is an array of objects
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
        });
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
</style>
