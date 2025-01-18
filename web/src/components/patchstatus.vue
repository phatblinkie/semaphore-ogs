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
        key="Patchstatus"
        :to="`/project/${projectId}/patchstatus`"
      >
        Patch Status
      </v-tab>
    </v-tabs>

    <v-container class="align-left">
      <v-row>
        <v-col cols="3">
          <v-toolbar flat>
            <v-toolbar-title>Hosts Software Status</v-toolbar-title>
          </v-toolbar>
          <div class="host-list">
            <ul>
              <li v-for="host in hosts" :key="host.hostname" class="host-item">
                <div class="host-item-content">
                  <v-btn
                    small
                    outlined
                    class="host-btn"
                    :class="{ 'active-btn': selectedHost === host.hostname }"
                    @click="onHostSelected(host.hostname, host.os_type)"
                  >
                    <span class="truncate">{{ host.hostname }}</span>
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
                </div>
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
              <div class="search-bar">
                <v-text-field
                  v-model="availableUpdatesSearch"
                  label="Search"
                  dense
                  hide-details
                  @input="searchAvailableUpdates"
                ></v-text-field>
              </div>
              <v-simple-table>
                <thead>
                  <tr>
                    <th class="text-left">Name</th>
                    <th v-if="isLinuxHost" class="text-left">Version</th>
                    <th v-if="isLinuxHost" class="text-left">Repo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(update, index) in filteredAvailableUpdates" :key="index">
                    <td class="text-left">{{ update.name || update }}</td>
                    <td v-if="isLinuxHost" class="text-left">{{ update.version }}</td>
                    <td v-if="isLinuxHost" class="text-left">{{ update.repo }}</td>
                  </tr>
                </tbody>
              </v-simple-table>
            </div>

            <div v-if="showInstalledUpdates" style="margin-top: 20px;">
              <div class="search-bar">
                <v-text-field
                  v-model="installedUpdatesSearch"
                  label="Search"
                  dense
                  hide-details
                  @input="searchInstalledUpdates"
                ></v-text-field>
              </div>
              <v-simple-table>
                <thead>
                  <tr>
                    <th class="text-left">Name</th>
                    <th v-if="isLinuxHost" class="text-left">Version</th>
                    <th v-if="isLinuxHost" class="text-left">Repo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(installed, index) in filteredInstalledUpdates" :key="index">
                    <td class="text-left">{{ installed.name || installed }}</td>
                    <td v-if="isLinuxHost" class="text-left">{{ installed.version }}</td>
                    <td v-if="isLinuxHost" class="text-left">{{ installed.repo }}</td>
                  </tr>
                </tbody>
              </v-simple-table>
            </div>
          </div>
          <div v-else>
            <v-toolbar flat>
              <v-toolbar-title>Select a host to see details.</v-toolbar-title>
            </v-toolbar>
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
  computed: {
    isLinuxHost() {
      return this.hostDetails && this.hostDetails.os_type === 'linux';
    },
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

    async onHostSelected(hostname, osType) {
      this.selectedHost = hostname;
      this.activeTable = 'available';
      this.showAvailableUpdates = true;
      this.showInstalledUpdates = false;
      this.clearSearchFields();
      await this.fetchHostDetails(hostname, osType);
    },

    async fetchHostDetails(hostname, osType) {
      try {
        const resp = await axios.get(
          `/post/get_combined_patch_status.php?project_id=${this.projectId}&hostname=${hostname}&os_type=${osType}`,
        );
        this.hostDetails = resp.data;
        this.hostDetails.os_type = osType; // Ensure os_type is set correctly
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
      this.filteredAvailableUpdates = this.hostDetails.updates.filter((update) => {
        const name = update.name || update;
        const version = update.version || '';
        const repo = update.repo || '';
        return (
          name.toLowerCase().includes(this.availableUpdatesSearch.toLowerCase())
          || (this.isLinuxHost && version.toLowerCase().includes(this.availableUpdatesSearch.toLowerCase()))
          || (this.isLinuxHost && repo.toLowerCase().includes(this.availableUpdatesSearch.toLowerCase()))
        );
      });
    },

    searchInstalledUpdates() {
      if (!this.installedUpdatesSearch) {
        this.filteredInstalledUpdates = this.hostDetails.installedUpdates || [];
        return;
      }
      this.filteredInstalledUpdates = this.hostDetails.installedUpdates.filter((installed) => {
        const name = installed.name || installed;
        const version = installed.version || '';
        const repo = installed.repo || '';
        return (
          name.toLowerCase().includes(this.installedUpdatesSearch.toLowerCase())
          || (this.isLinuxHost && version.toLowerCase().includes(this.installedUpdatesSearch.toLowerCase()))
          || (this.isLinuxHost && repo.toLowerCase().includes(this.installedUpdatesSearch.toLowerCase()))
        );
      });
    },

    toggleTable(table) {
      this.activeTable = table;
      if (table === 'available') {
        this.showAvailableUpdates = true;
        this.showInstalledUpdates = false;
        this.installedUpdatesSearch = ''; // Reset search field
        this.filteredAvailableUpdates = this.hostDetails.updates || [];
      } else if (table === 'installed') {
        this.showInstalledUpdates = true;
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
.host-item-content {
  display: flex;
  align-items: center;
  gap: 10px;
}
.host-btn {
  width: 250px; /* Set a fixed width for the buttons */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  text-align: left; /* Left-align the text inside the button */
}
.truncate {
  display: inline-block;
  max-width: 80%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
.update-items .update-item:nth-child(odd) {
  background-color: #f9f9f9;
}
.update-items .update-item:nth-child(even) {
  background-color: #e9e9e9;
}
.search-bar {
  margin-bottom: 10px;
}
.active-btn {
  background-color: #1976d2 !important;
  color: white !important;
}
</style>
