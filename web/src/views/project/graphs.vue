<template>
  <div id="app">
    <Graphs msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import graphs from '@/components/graphs.vue';

export default {
  name: 'App',
  components: {
    graphs,
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 0px;
}
</style>
