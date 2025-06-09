<script setup>
import { RouterView } from 'vue-router'
import montain from '../assets/imgs/mountain_1244-430-min.png'
</script>

<template>
  <div class="relative min-h-screen">
    <div class="absolute bottom-0 left-1/2 -translate-x-1/2">
      <img :src="montain" />
    </div>

    <div class="relative z-10 min-h-screen flex items-center justify-center px-8 py-8">
      <RouterView></RouterView>
    </div>
  </div>
</template>

<style scoped></style>
