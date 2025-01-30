import React, { useState } from 'react';
import axios from 'axios';

const Rent = () => {
    // State Definitionen
    const [isLoading, setIsLoading] = useState(false);
    const [formData, setFormData] = useState({
        businessName: '',
        contactPerson: '',
        email: '',
        phone: '',
        productType: '',
        spaceType: '',
        message: ''
    });

    const [submitStatus, setSubmitStatus] = useState({
        type: '',
        message: ''
    });

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);

        try {
            const response = await axios.post('https://housnkuh.de/api/submit-rental.php', formData);
            
            if (response.data.success) {
                setSubmitStatus({
                    type: 'success',
                    message: 'Ihre Anfrage wurde erfolgreich gesendet!'
                });
                // Formular zurücksetzen
                setFormData({
                    businessName: '',
                    contactPerson: '',
                    email: '',
                    phone: '',
                    productType: '',
                    spaceType: '',
                    message: ''
                });
            }
        } catch (error) {
            setSubmitStatus({
                type: 'error',
                message: 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.'
            });
        } finally {
            setIsLoading(false);
        }
    };

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    return (
        <div className="py-12 bg-gray-50">
            <div className="max-w-3xl mx-auto px-4">
                <h2 className="text-3xl font-bold text-center mb-8">Verkaufsfläche mieten</h2>
                
                {/* Status Message */}
                {submitStatus.type && (
                    <div className={`mb-6 p-4 rounded-md ${
                        submitStatus.type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                    }`}>
                        {submitStatus.message}
                    </div>
                )}

                <div className="bg-white rounded-lg shadow-md p-8">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Firmenname</label>
                                <input
                                    type="text"
                                    name="businessName"
                                    value={formData.businessName}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Ansprechpartner</label>
                                <input
                                    type="text"
                                    name="contactPerson"
                                    value={formData.contactPerson}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">E-Mail</label>
                                <input
                                    type="email"
                                    name="email"
                                    value={formData.email}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">Telefon</label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value={formData.phone}
                                    onChange={handleChange}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Art der Produkte</label>
                            <input
                                type="text"
                                name="productType"
                                value={formData.productType}
                                onChange={handleChange}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Gewünschte Verkaufsfläche</label>
                            <select
                                name="spaceType"
                                value={formData.spaceType}
                                onChange={handleChange}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Bitte wählen</option>
                                <option value="regal-a">Verkaufsblock Lage A (35€/Monat)</option>
                                <option value="regal-b">Verkaufsblock Lage B (15€/Monat)</option>
                                <option value="kuehl">Verkaufsblock gekühlt (50€/Monat)</option>
                                <option value="tisch">Verkaufsblock Tisch (40€/Monat)</option>
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">Ihre Nachricht</label>
                            <textarea
                                name="message"
                                value={formData.message}
                                onChange={handleChange}
                                rows="4"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            ></textarea>
                        </div>

                        <div>
                            <button
                                type="submit"
                                disabled={isLoading}
                                className={`w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                    isLoading ? 'opacity-50 cursor-not-allowed' : ''
                                }`}
                            >
                                {isLoading ? 'Wird gesendet...' : 'Anfrage senden'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default Rent;