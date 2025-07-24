import $ from 'jquery';
import { SeoData } from '../../types';

export class SeoFormView {
    private sendButton: JQuery;
    private answerContainer: JQuery;
    private consoleTextarea: JQuery;
    private checkboxes: JQuery;
    private acceptCallback: ((type: string, value: string) => void) | null = null;

    constructor() {
        this.sendButton = $('#wssa-send-button');
        this.answerContainer = $('#wssa-agent-answer');
        this.consoleTextarea = $('#wssa_agent_console');
        this.checkboxes = $('input[type="checkbox"][name^="wssa_seo_"]');
    }

    public getFormData(): string {
        const requests: string[] = [];
        this.checkboxes.filter(':checked').each(function() {
            const label = $(`label[for='${this.id}']`).text();
            if (label) {
                requests.push(label.trim().toLowerCase());
            }
        });

        let requestMessage = requests.join(', ');
        const additionalInfo = this.consoleTextarea.val();

        if (typeof additionalInfo === 'string' && additionalInfo.trim() !== '') {
            requestMessage += (requestMessage ? '. ' : '') + `Additional info: ${additionalInfo.trim()}`;
        }

        return requestMessage;
    }

    public renderResults(seoData: SeoData): void {
        this.removeLoadingIndicator();

        if (!this.answerContainer.is(':empty')) {
            this.renderSeparator();
        }

        let key = this.generateSimpleUniqueKey();
        let suggestionsHtml = '';
        if (seoData.title) {
            suggestionsHtml += `<p><strong>Title:</strong> ${seoData.title} ${this.getAcceptButton(seoData.title, 'title', key)}</p>`;
        }
        if (seoData.description) {
            suggestionsHtml += `<p><strong>Description:</strong> ${seoData.description} ${this.getAcceptButton(seoData.description, 'description', key)}</p>`;
        }
        if (seoData.shortDescription) {
            suggestionsHtml += `<p><strong>Short Description:</strong> ${seoData.shortDescription} ${this.getAcceptButton(seoData.shortDescription, 'short_description', key)}</p>`;
        }
        if (seoData.keywords) {
            suggestionsHtml += `<p><strong>Keywords:</strong> ${seoData.keywords} ${this.getAcceptButton(seoData.keywords, 'keywords', key)}</p>`;
        }

        let html = '<div>';
        if (suggestionsHtml) {
            html += '<h4>SEO Suggestions</h4>';
            html += suggestionsHtml;
            html += '<button class="button accept-all-changes" data-key="' + key + '" style="display: inline-flex; align-items: center; text-align: center; gap: 5px">Accept all<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-check" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/></svg></button>';
            html += '<hr style="height: 1px; background-color: #ccd0d4; border: none; margin: 16px 0;">';
        }

        html += '<h4>Summary</h4>';
        html += `<p>${seoData.summary || 'No summary provided.'}</p>`;
        html += '</div>';

        this.answerContainer.append(html);
        this.scrollToBottom();
    }

    public renderError(message: string): void {
        this.renderMessage(message, 'Error', 'red');
    }

    public renderSuccess(message: string): void {
        this.renderMessage(message, 'Success', 'green');
    }

    public renderMessage(message: string, type: string, color: string): void {
        this.removeLoadingIndicator();

        this.renderSeparator();

        this.answerContainer.append(`<div style="color: ${color};"><strong>${type}:</strong> ${message}</div>`);
        this.scrollToBottom();
    }

    public renderWorkInProgress(message: string): void {
        const loadingHtml = `<p class="wssa-loading-indicator"><em>${message}</em></p>`;
        this.answerContainer.append(loadingHtml);
        this.scrollToBottom();
    }

    public renderSeparator(): void {
        this.answerContainer.append('<hr style="height: 3px; background-color: #ccd0d4; border: none; margin: 16px 0;">');
    }

    public toggleLoading(isLoading: boolean): void {
        this.sendButton.prop('disabled', isLoading);
        if (isLoading) {
            this.renderWorkInProgress('Generating SEO data, please wait...');
        }
    }

    public toggleImplementing(isImplementing: boolean): void {
        this.answerContainer.find('.accept-changes').prop('disabled', isImplementing);
        this.answerContainer.find('.accept-all-changes').prop('disabled', isImplementing);

        if (isImplementing) {
            this.renderWorkInProgress('Implementing SEO data, please wait...');
        }
    }

    public onSendClick(callback: () => void): void {
        this.sendButton.on('click', callback);
    }

    public onAcceptClick(callback: (type: string, value: string) => void): void {
        this.acceptCallback = callback;
        this.answerContainer.off('click', '.accept-changes').on('click', '.accept-changes', (e) => {
            e.preventDefault();
            const button = $(e.currentTarget);
            const type = button.data('type');
            const value = button.data('text');
            
            if (this.acceptCallback && type && value) {
                this.acceptCallback(type, value);
            }
        });

        this.answerContainer.off('click', '.accept-all-changes').on('click', '.accept-all-changes', (e) => {
            e.preventDefault();
            if (this.acceptCallback) {
                const acceptAllButton = $(e.currentTarget);
                this.answerContainer.find('.accept-changes-' + acceptAllButton.data('key')).each((_, el) => {
                    const button = $(el);
                    const type = button.data('type');
                    const value = button.data('text');
                    if (type && value && this.acceptCallback) {
                        this.acceptCallback(type, value);
                    }
                });
            }
        });
    }

    private removeLoadingIndicator(): void {
        this.answerContainer.find('.wssa-loading-indicator').remove();
    }

    private scrollToBottom(): void {
        this.answerContainer.scrollTop(this.answerContainer[0].scrollHeight);
    }

    private getAcceptButton(text: string, type: string, key: string): string {
        return '<button class="button accept-changes accept-changes-' + key + '" data-text="' + text + '" data-type="' + type + '" style="display: inline-flex; align-items: center; gap: 5px"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16"><path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/><path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/></svg></button>';
    }

    private generateSimpleUniqueKey(): string {
        return Date.now().toString(36) + Math.random().toString(36).substring(2);
    }
}
