import { agentService } from '../services/AgentService';
import { SeoFormView } from '../views/SeoFormView';

export class SeoFormComponent {
    private view: SeoFormView;

    constructor() {
        this.view = new SeoFormView();
    }

    public init(): void {
        this.view.onSendClick(() => this.handleSendClick());
        this.view.onAcceptClick((type: string, value: string) => this.handleAcceptClick(type, value));
    }

    private async handleSendClick(): Promise<void> {
        const requestMessage = this.view.getFormData();
        if (!requestMessage) {
            this.view.renderError('Please select at least one SEO option to generate.');
            return;
        }

        this.view.toggleLoading(true);

        try {
            const seoData = await agentService.generateSeo(requestMessage);
            this.view.renderResults(seoData);
        } catch (error: any) {
            this.view.renderError(error.message || 'An unknown error occurred.');
        } finally {
            this.view.toggleLoading(false);
        }
    }

    private handleAcceptClick(type: string, value: string): void {
        console.log('Accept button clicked for type:', type);
        console.log('Accept button clicked for value:', value);
    }
}
