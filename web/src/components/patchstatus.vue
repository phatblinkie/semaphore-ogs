<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title> Patch Status </v-toolbar-title>
    </v-toolbar>
    <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="20"
      :options.sync="tableOptions"
      class="mt-4"
      dense
      :footer-props="{
        'items-per-page-options': [20, 50, 100],
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
      <template v-slot:item.pending_updates="{ item }">
        <span>{{ item.pending_updates }}</span>
      </template>
      <template v-slot:item.installed_updates="{ item }">
        <span>{{ item.installed_updates }}</span>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      projectId: this.$route.params.projectId,
      headers: [
        { text: 'Hostname', value: 'hostname', width: '120px' },
        { text: 'Pending Updates', value: 'pending_updates', width: 'auto' },
        { text: 'Installed Updates', value: 'installed_updates', width: 'auto' },
      ],
      items: [],
      tableOptions: {
        sortBy: ['hostname'],
        sortDesc: [false],
      },
    };
  },
  mounted() {
    this.fetchData();
    this.interval = setInterval(this.fetchData, 15000);
  },
  beforeDestroy() {
    clearInterval(this.interval);
  },
  methods: {
    fetchData() {
      axios
        .get(`/post/get_patch_status_hosts.php?project_id=${this.projectId}`)
        .then((response) => {
          this.items = Array.isArray(response.data) ? response.data : [];
        })
        .catch((error) => {
          console.error('Error fetching data:', error);
          this.items = [];
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
  text-align: left;
  padding: 8px;
}
</style>
