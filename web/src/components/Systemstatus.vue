<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title> System Status </v-toolbar-title>
    </v-toolbar>
    <v-tabs show-arrows class="pl-4">
      <v-tab key="Systemstatus" :to="`/project/${projectId}/systemstatus`">
        System Status
      </v-tab>
      <v-tab key="Patchstatus" :to="`/project/${projectId}/patchstatus`">Patch Status</v-tab>
    </v-tabs>
    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="30"
      :options.sync="tableOptions"
      class="mt-4"
      dense
      :footer-props="{
        'items-per-page-options': [30, 50, 100],
        'items-per-page-text': 'Rows per page:',
        'show-first-last-page': true,
        'show-current-page': true,
        'show-items-per-page': true,
        'show-select': true,
        'align': 'left'
      }"
    >
      <template v-slot:item.hostname="{ item }">
        <router-link :to="`/project/${projectId}/host/${item.hostname}`">{{
          item.hostname
        }}</router-link>
      </template>
      <template v-slot:item.ansible_ping="{ item }">
        <TaskStatus :status="item.ansible_ping" />
      </template>
      <template v-slot:item.disk_capacity="{ item }">
        <div class="disk-warning" v-if="isDiskOver90(item.disk_capacity)">
          <v-icon color="red">mdi-alert</v-icon>
        </div>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon>
              <v-icon>mdi-information</v-icon>
            </v-btn>
          </template>
          <div v-if="item.ansible_ping !== 'unreachable'" class="disk-meter tooltip-content">
            <div v-for="(disk, index) in parseDiskCapacity(item.disk_capacity)" :key="index" class="disk-item">
              <div class="disk-info">
                <span class="disk-label">{{ disk.name }}</span>
                <span class="disk-percent">{{ disk.used }}%</span>
              </div>
              <meter
                :value="disk.used"
                :min="0"
                :max="100"
                :low="70"
                :high="90"
                :optimum="0"
                class="meter"
              ></meter>
            </div>
          </div>
          <div v-else>
            N/A
          </div>
        </v-tooltip>
      </template>

      <template v-slot:item.proc_usage="{ item }">
        <div v-if="item.ansible_ping !== 'unreachable'" class="proc-meter">
          <span class="proc-label">{{ roundProcUsage(item.proc_usage) }}%</span>
          <meter
            :value="roundProcUsage(item.proc_usage)"
            :min="0"
            :max="100"
            :low="70"
            :high="90"
            :optimum="0"
            class="meter"
          ></meter>
        </div>
        <div v-else>
          N/A
        </div>
      </template>
      <template v-slot:item.app_check="{ item }">
        <div v-if="item.app_check">
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <div v-bind="attrs" v-on="on">
                <AppCheckStatus :status="getOverallStatus(item.app_check)" />
              </div>
            </template>
            <div>
              <div v-for="(service, index) in parseAppCheck(item.app_check)" :key="index">
                {{ service.name }}: {{ service.status }}
              </div>
            </div>
          </v-tooltip>
        </div>
        <div v-else>
          No services monitored
        </div>
      </template>
      <template v-slot:item.last_responded="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <span v-bind="attrs" v-on="on">{{ formatTimeDifference(item.last_responded) }}</span>
          </template>
          <span>{{ formatTimestamp(item.last_responded) }}</span>
        </v-tooltip>
      </template>
      <template v-slot:item.uptime="{ item }">
        <span v-if="item.ansible_ping !== 'unreachable'">{{ formatUptime(item.uptime) }}</span>
        <span v-else>N/A</span>
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
        { text: 'Hostname', value: 'hostname', width: '120px' },
        { text: 'Ansible Ping', value: 'ansible_ping', width: '100px' },
        { text: 'Disk Usage', value: 'disk_capacity', width: '150px' }, // Adjusted width
        { text: 'Proc Usage', value: 'proc_usage', width: '150px' },
        { text: 'App Check', value: 'app_check', width: '100px' },
        { text: 'Last Responded', value: 'last_responded', width: '150px' }, // Adjusted width
        { text: 'Uptime', value: 'uptime', width: '200px' },
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
    formatTimeDifference(timestamp) {
      if (!timestamp) return 'N/A'; // Return 'N/A' if timestamp is undefined or null
      const date = new Date(timestamp.replace(' ', 'T')); // Replace space with 'T' to make it ISO 8601 compliant
      const now = new Date();
      const diff = Math.abs(now - date);
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(minutes / 60);
      const days = Math.floor(hours / 24);
      if (days > 0) {
        return `${days}d ${hours % 24}h ${minutes % 60}m ago`;
      } if (hours > 0) {
        return `${hours}h ${minutes % 60}m ago`;
      }
      return `${minutes}m ago`;
    },
    formatUptime(uptime) {
      if (uptime === null) return 'N/A'; // Return 'N/A' if uptime is null
      // const seconds = uptime % 60;
      const minutes = Math.floor((uptime % 3600) / 60);
      const hours = Math.floor((uptime % 86400) / 3600);
      const days = Math.floor(uptime / 86400);
      return `${days}d ${hours}h ${minutes}m`;
    },
    parseDiskCapacity(diskCapacity) {
      return diskCapacity.split(', ').map((disk) => {
        const [name, used] = disk.split(' ');
        return { name, used: parseInt(used, 10) };
      });
    },
    isDiskOver90(diskCapacity) {
      return this.parseDiskCapacity(diskCapacity).some((disk) => disk.used > 90);
    },
    parseAppCheck(appCheck) {
      if (!appCheck) return [];
      return appCheck.split(',').map((service) => {
        const [name, status] = service.split(':').map((s) => s.trim());
        return { name: name || 'Unknown', status: status || 'Unknown' };
        //        return {
        //          name: name || 'Unknown',
        //          status: status && (status.toLowerCase().includes('stopped') || status.toLowerCase().includes('failed') || status.toLowerCase().includes('inactive')
        //           || status.toLowerCase() === 'started') ? status : 'Success',
        //        };
      });
    },
    //    getOverallStatus(appCheck) {
    //      const services = this.parseAppCheck(appCheck);
    //      return services.some((service) => service.status.toLowerCase().includes('stopped') || service.status.toLowerCase().includes('unknown') || service.status.toLowerCase().includes('unreachable')
    //        || service.status.toLowerCase().includes('failed') || service.status.toLowerCase() === 'inactive') ? 'Failed' : 'Success';
    //    },
    getOverallStatus(appCheck) {
      const services = this.parseAppCheck(appCheck);
      if (services.some((service) => service.status.toLowerCase().includes('unreachable'))) {
        // console.log('unreachable');
        return 'N/A';
      }
      if (services.some((service) => service.status.toLowerCase().includes('stopped') || service.status.toLowerCase().includes('unknown')
        || service.status.toLowerCase().includes('failed') || service.status.toLowerCase() === 'inactive')) {
        // console.log('failed');
        return 'Failed';
      }
      return 'Success';
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
.disk-meter,
.proc-meter {
  display: flex;
  align-items: center;
  width: 100%;
}
.disk-meter {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 100%;
}
.disk-item {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 4px;
  width: 100%;
}
.disk-info {
  display: flex;
  justify-content: space-between;
  width: 100%;
}
.disk-label,
.proc-label {
  font-weight: bold;
  margin-right: 8px;
}
.disk-percent {
  margin-left: auto;
}
.disk-meter meter,
.proc-meter meter {
  flex-grow: 1;
  height: 20px;
  width: 100%;
}
.tooltip-content {
  width: 500px;
  height: auto;
}
.disk-warning {
  display: inline-block;
  margin-right: 8px;
}
</style>
