// Type declarations for CSS modules
declare module '*.css' {
  const content: any;
  export default content;
}

// Type declarations for Stimulus
declare global {
  interface Window {
    Stimulus: any;
  }
}

export {};
