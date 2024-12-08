<template>
  <div id="app">
    <Compliance msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import Compliance from '@/components/compliance.vue';

export default {
  name: 'App',
  components: {
    Compliance,
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
