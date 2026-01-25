<template>
  <router-link
    :to="to"
    class="group relative flex items-center gap-3 px-4 py-2.5 rounded-lg
           text-sm font-medium tracking-wide
           text-slate-300
           transition-colors duration-200
           hover:bg-white/5 hover:text-white"
    active-class="bg-cyan-500/10 text-cyan-300"
  >

    <span
      class="absolute left-0 top-1/2 -translate-y-1/2
             h-6 w-1 rounded-full
             bg-cyan-400
             opacity-0
             router-link-active:opacity-100"
    ></span>

    <span
      class="flex items-center justify-center
             w-5 h-5
             text-slate-400
             group-hover:text-cyan-300
             transition-colors duration-200"
    >
      <slot />
    </span>

    <span class="truncate">
      {{ label }}
    </span>
  </router-link>
</template>

<script>
export default {
  name: 'SidebarLink',
  props: {
    to: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      required: true,
    },
  },
}
</script>
