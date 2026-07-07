import "./App.css";
import OnboardingQuestions from "./components/OnboardingQuestions";

function App() {
  return (
    <div className="container resources">
      <h2 className="resources__heading">Find Your Learning Modules</h2>
      <p className="resources__eyebrow">Answer a few quick questions to get modules matched to your conversation</p>
      <OnboardingQuestions />
    </div>
  );
}

export default App;
