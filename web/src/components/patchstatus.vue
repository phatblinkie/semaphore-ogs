<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title>Patch Status</v-toolbar-title>
    </v-toolbar>

    <v-tabs show-arrows class="pl-4">
      <v-tab
        key="Systemstatus"
        :to="`/project/${projectId}/systemstatus`"
      >
        System Status
      </v-tab>
      <v-tab
        key="TaskGraphs"
        :to="`/project/${projectId}/taskgraphs`"
      >
        Task Graphs
      </v-tab>
      <v-tab
        key="Patchstatus"
        :to="`/project/${projectId}/patchstatus`"
      >
        Patch Status
      </v-tab>
      <v-tab
        key="Compliancestatus"
        :to="`/project/${projectId}/compliancestatus`"
      >
        Compliance Status
      </v-tab>
    </v-tabs>

    <v-container class="align-left">
      <v-row>
        <v-col cols="3">
          <h2>Hosts and Available Updates</h2>
          <div class="host-list">
            <ul>
              <li v-for="host in hosts" :key="host.hostname" class="host-item">
                <v-btn
                  small
                  outlined
                  :class="{ 'active-btn': selectedHost === host.hostname }"
                  @click="onHostSelected(host.hostname)"
                >
                  {{ host.hostname }}
                </v-btn>
                <v-chip
                  :color="host.available_updates === 0 ? 'green' : 'teal'"
                  label
                  pill
                  class="ma-2"
                >
                  {{ host.available_updates }}
                </v-chip>
                <v-badge
                  :content="host.formatted_timestamp"
                  color="secondary"
                >
                </v-badge>
              </li>
            </ul>
          </div>
        </v-col>

        <v-col cols="9">
          <div v-if="hostDetails">
            <div style="display: flex; gap: 20px; margin-top: 20px;">
              <v-btn
                small
                outlined
                :class="{ 'active-btn': activeTable === 'available' }"
                @click="toggleTable('available')"
              >
                Available Updates
              </v-btn>
              <v-btn
                small
                outlined
                :class="{ 'active-btn': activeTable === 'installed' }"
                @click="toggleTable('installed')"
              >
                Installed Updates
              </v-btn>
            </div>

            <div v-if="showAvailableUpdates" style="margin-top: 20px;">
              <div class="update-list">
                <div class="search-bar">
                  <v-text-field
                    v-model="availableUpdatesSearch"
                    label="Search"
                    dense
                    hide-details
                    @input="searchAvailableUpdates"
                  ></v-text-field>
                </div>
                <ul class="update-items">
                  <li v-if="filteredAvailableUpdates.length === 0">No updates available</li>
                  <li v-else v-for="(updateItem, index) in filteredAvailableUpdates" :key="index">
                    {{ updateItem }}
                  </li>
                </ul>
              </div>
            </div>

            <div v-if="showInstalledUpdates" style="margin-top: 20px;">
              <div class="update-list">
                <div class="search-bar">
                  <v-text-field
                    v-model="installedUpdatesSearch"
                    label="Search"
                    dense
                    hide-details
                    @input="searchInstalledUpdates"
                  ></v-text-field>
                </div>
                <ul class="update-items">
                  <li v-if="filteredInstalledUpdates.length === 0">No installed updates</li>
                  <li v-else v-for="(installed, index) in filteredInstalledUpdates" :key="index">
                    {{ installed }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div v-else>
            <p>Select a host to see details.</p>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PatchStatus',
  props: {
    projectId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      hosts: [],
      hostDetails: null,
      showAvailableUpdates: false,
      showInstalledUpdates: false,
      availableUpdatesSearch: '',
      installedUpdatesSearch: '',
      filteredAvailableUpdates: [],
      filteredInstalledUpdates: [],
      selectedHost: null,
      activeTable: 'available',
      headers: [
        { text: 'Host', value: 'hostname' },
        {
          text: 'Available Updates',
          value: 'available_updates',
        },
      ],
    };
  },
  created() {
    this.fetchHosts();
  },
  methods: {
    async fetchHosts() {
      try {
        const resp = await axios.get(
          `/post/get_patch_status_hosts.php?project_id=${this.projectId}`,
        );
        this.hosts = resp.data;
      } catch (error) {
        console.error('Error fetching hosts:', error);
      }
    },

    async onHostSelected(hostname) {
      this.selectedHost = hostname;
      this.activeTable = 'available';
      this.showAvailableUpdates = true;
      this.showInstalledUpdates = false;
      this.clearSearchFields();
      await this.fetchHostDetails(hostname);
    },

    async fetchHostDetails(hostname) {
      try {
        const resp = await axios.get(
          `/post/get_combined_patch_status.php?project_id=${this.projectId}&hostname=${hostname}`,
        );
        this.hostDetails = resp.data;
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
      } catch (error) {
        console.error('Error fetching host details:', error);
      }
    },

    searchAvailableUpdates() {
      if (!this.availableUpdatesSearch) {
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
        return;
      }
      this.filteredAvailableUpdates = this.hostDetails.updates.filter((update) => update.toLowerCase().includes(this.availableUpdatesSearch.toLowerCase()));
    },

    searchInstalledUpdates() {
      if (!this.installedUpdatesSearch) {
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
        return;
      }
      this.filteredInstalledUpdates = this.hostDetails.installedUpdates.filter((installed) => installed.toLowerCase().includes(this.installedUpdatesSearch.toLowerCase()));
    },

    toggleTable(table) {
      this.activeTable = table;
      if (table === 'available') {
        this.showAvailableUpdates = !this.showAvailableUpdates;
        this.showInstalledUpdates = false;
        this.installedUpdatesSearch = ''; // Reset search field
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
      } else if (table === 'installed') {
        this.showInstalledUpdates = !this.showInstalledUpdates;
        this.showAvailableUpdates = false;
        this.availableUpdatesSearch = ''; // Reset search field
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
      }
    },

    clearSearchFields() {
      this.availableUpdatesSearch = '';
      this.installedUpdatesSearch = '';
    },
  },
  watch: {
    showAvailableUpdates(newVal) {
      if (newVal) {
        this.installedUpdatesSearch = ''; // Reset search field
      }
    },
    showInstalledUpdates(newVal) {
      if (newVal) {
        this.availableUpdatesSearch = ''; // Reset search field
      }
    },
    installedUpdatesSearch(newVal) {
      // If search is cleared, ensure we still have an array
      if (!newVal) {
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
      }
    },
  },
  beforeDestroy() {
    this.clearSearchFields();
  },
};
</script>

<style scoped>
/*
  Forces a fixed minimal table width and avoids horizontal scroll:
  - min-width ensures the table won't shrink below that width.
  - overflow: hidden and white-space: normal let cells wrap text
    instead of scrolling.
*/
.fixed-table {
  min-width: 1200px;
  table-layout: fixed;
  border-collapse: collapse;
  overflow: hidden;
}
.fixed-table th,
.fixed-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
  white-space: normal;
  text-overflow: ellipsis;
  vertical-align: top;
}
/* Remove/hide horizontal scroll bars on smaller screens */
html,
body {
  overflow-x: hidden;
}
.fixed-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}
.align-left {
  margin-left: 0;
}
.host-list ul {
  list-style-type: none;
  padding: 0;
}
.host-list li {
  margin-bottom: 2px; /* Reduced margin */
  text-align: left;
}
.host-details div {
  margin-bottom: 10px;
}
.update-list ul {
  list-style-type: none;
  padding: 0;
}
.update-list li {
  margin-bottom: 10px;
}
.update-items {
  text-align: left;
}
.search-bar {
  margin-bottom: 10px;
}
.active-btn {
  background-color: #1976d2 !important;
  color: white !important;
}
</style>
